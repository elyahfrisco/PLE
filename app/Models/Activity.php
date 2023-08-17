<?php

namespace App\Models;

use App\Models\Pass;
use App\Models\Planning;
use App\Models\Establishment;
use App\Models\AppModel;
use App\Models\ActivitySubscriptionCondition;
use App\Models\ActivitySubscriptionQuestionnaire;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Activity extends AppModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sigle',
        'description',
        'full_description',
        'condition',
        'duration',
        'background_color',
        'font_color',
        'care',
        'for_kid',
        'image',
    ];

    public function subscriptionConditions()
    {
        return $this->hasMany(ActivitySubscriptionCondition::class);
    }

    public function subscriptionQuestionnaires()
    {
        return $this->hasMany(ActivitySubscriptionQuestionnaire::class);
    }

    public function establishments()
    {
        return $this->belongsToMany(Establishment::class, 'establishment_activity');
    }

    public function passes()
    {
        return $this->belongsToMany(Pass::class, 'pass_activity')->withPivot('number_activity_sessions');
    }

    public function activity_group()
    {
        return $this->belongsTo(ActivityPassGroup::class, 'activity_id', 'group_id', 'pass_id');
    }

    public function plannings()
    {
        return $this->hasMany(Planning::class);
    }

    public function activity_sessions()
    {
        return $this->hasMany(ActivitySessions::class);
    }

    public function activities_for_recuperation()
    {
        return $this->belongsToMany(Activity::class, 'recuperation_authorized_for_activities', 'activity_id', 'activity_for_recuperation_id')->select(['name', 'id']);
    }

    public function activities_for_recuperation_id()
    {
        return array_merge($this->activities_for_recuperation->pluck('id')->toArray(), [$this->id]);
    }

    public function scopeSearch($query)
    {
        return $query->when(request()->q, function ($query, $q) {
            $query->whereRaw("name LIKE '%$q%'");
        });
    }
    public function scopeFilter($query)
    {
        return $query->when(request()->not_id, function ($query, $not_id) {
            $query->where("id", '!=', $not_id);
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($activity) {
            forget_cache("activities_styles");
        });
        static::updated(function ($activity) {
            forget_cache("activities_styles");
        });
        static::deleted(function ($activity) {
            forget_cache("activities_styles");
        });
    }
}
