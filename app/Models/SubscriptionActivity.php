<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Activity;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SubscriptionActivity extends AppModel
{
  use HasFactory;
  protected $fillable = [
    "date",
    "session_count",
    "activity_session_id",
    "planning_id",
    "establishment_id",
    "subscription_id",
    "user_id",
    "activity_id",
    "pass_id",
    "accomplished",
    'is_recuperation',
    'absence_prevention_id',
    'absence_prevention_date',
    'time_start',
    'time_end',
    'is_debited',
    'queued_at',
    'can_catch_up_until',
    'session_status_txt',
    'presence_status_txt',
    'price',
    'is_first',
  ];

  protected $appends = ['schedule', 'presence', 'day_name', 'group_name', 'session_status_txt_fr', 'presence_status_txt_fr'];

  public function getDayNameAttribute()
  {
    return substr(dToFr(Carbon::parse($this->time_start)->format('l')), 0, 2);
  }

  public function getPresenceStatusTxtFrAttribute()
  {
    switch ($this->presence_status_txt) {
      case 'present':
        return "present";
      case 'debited':
        return 'absent';
    }
    return null;
  }

  public function getSessionStatusTxtFrAttribute()
  {
    switch ($this->session_status_txt) {
      case 'queued':
        return "file d'attente";
      case 'presence_for_recuperation_confirmed':
        return "présence pour la séance de récupération : confirmée";
      case 'debited':
        return 'debité';
    }
    return null;
  }

  public static function userActivitySessionsId($user_id)
  {
    return Cache::remember(
      "user_{$user_id}_activity_sessions_id",
      60 * 60 * 12,
      fn () => static::where('user_id', $user_id)
        ->pluck('activity_session_id')
    );;
  }

  public function SigleEstablishmentName()
  {
    return strtolower($this->establishment_name) == 'bordeaux' ? 'C' : $this->establishment_name[0];
  }

  public function getGroupNameAttribute()
  {
    return strtoupper($this->day_name) . Carbon::parse($this->time_start)->format('H\Hi') . $this->SigleEstablishmentName();
  }

  public function getPresenceAttribute()
  {
    return [
      "absence_prevented" => $this->absence_prevention_id ? true : false,
      "can_prevent_absence" => !$this->absence_prevention_id && $this->time_start > now(),
      "is_accomplished" => $this->accomplished,
      "is_recuperation" => $this->is_recuperation,
    ];
  }

  public function getScheduleAttribute()
  {
    return [
      'start' => Carbon::parse($this->time_start)->format('H:i'),
      'end' => Carbon::parse($this->time_end)->format('H:i'),
    ];
  }

  public function activity()
  {
    return $this->belongsTo(Activity::class);
  }

  public function session()
  {
    return $this->hasOne(ActivitySessions::class, 'id', 'activity_session_id');
  }

  public function user_subscription()
  {
    return $this->belongsTo(Subscription::class, 'subscription_id', 'id');
  }

  public function absence_prevention()
  {
    return $this->hasOne(AbsencePrevention::class, 'id', 'absence_prevention_id');
  }

  public function date_max($query){
    $query->where('subscription_activities.time_start', '>' , Carbon::now());
  }

  public function scopeUnrecoveredSessions($query)
  {
    $query->whereNotNull('subscription_activities.absence_prevention_id')
      ->where('session_status_txt', 'queued')
      ->join('queues', 'queues.absence_prevention_id', 'subscription_activities.absence_prevention_id')
      ->leftjoin('recuperation_requests', 'recuperation_requests.queue_id', 'queues.id')
      ->whereNull('recuperation_requests.presence_confirmed_at');
  }

  protected static function booted()
  {
    static::addGlobalScope('ancient', function ($query) {
      $query->leftJoin('establishments', 'subscription_activities.establishment_id', 'establishments.id')
        ->selectRaw('subscription_activities.*')
        ->addSelect(DB::raw('establishments.name establishment_name'));
    });

    static::deleting(function ($data) {
      optional($data->absence_prevention())->delete();
    });
  }


}
