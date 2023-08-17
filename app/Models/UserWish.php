<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AppModel;
use App\Models\Activity;
use App\Models\Establishment;
use App\Models\Planning;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserWish extends AppModel
{
    use HasFactory;
    protected $fillable = [
        'time_start',
        'time_end',
        'date_start',
        'planning_id',
        'activity_id',
        'establishment_id',
        'user_id',
        'day',
    ];

    protected $with = ['activity', 'establishment', 'planning'];
    protected $appends = ['dayFr'];

    public function getDayFrAttribute()
    {
        return dToFr($this->day);
    }

    public function getDayNameAttribute()
    {
        return substr(dToFr($this->day), 0, 2);
    }

    public function SigleEstablishmentName()
    {
        if ($this->relationLoaded('establishment')) {
            return strtolower($this->establishment->name) == 'bordeaux' ? 'C' : $this->establishment->name[0];
        } else if ($this->establishment_name) {
            return strtolower($this->establishment_name) == 'bordeaux' ? 'C' : $this->establishment_name[0];
        } else {
            return "";
        }
    }

    public function getGroupNameAttribute()
    {
        return strtoupper($this->day_name) . Carbon::parse($this->time_start)->format('H\Hi') . $this->SigleEstablishmentName();
    }

    public function activity()
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    public function establishment()
    {
        return $this->hasOne(Establishment::class, 'id', 'establishment_id');
    }

    public function planning()
    {
        return $this->hasOne(Planning::class, 'id', 'planning_id');
    }

    public function scopeJoinEstablishment($query)
    {
        $query->join('establishments', 'user_wishes.establishment_id', 'establishments.id')
            ->addSelect(DB::raw('establishments.name establishment_name'));
    }

    public function scopeJoinActivity($query)
    {
        $query->join('activities', 'user_wishes.activity_id', 'activities.id')
            ->addSelect(DB::raw('activities.name activity_name'));
    }

    public function scopeJoinPlanning($query)
    {
        $query->join('plannings', 'user_wishes.planning_id', 'plannings.id')
            ->addSelect(DB::raw('plannings.search_key, DAYOFWEEK(plannings.start_at) day_of_week'));
    }

    public function scopeQueryExport($query)
    {
        $query
            ->without('activity', 'establishment', 'planning')
            ->selectRaw('user_wishes.id, user_wishes.user_id')
            ->JoinEstablishment()
            ->JoinActivity()
            ->JoinPlanning();
    }
}
