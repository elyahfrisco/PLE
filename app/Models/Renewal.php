<?php

namespace App\Models;

use App\Models\Establishment;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Renewal extends AppModel
{
    use HasFactory;

    protected $fillable = [
        "stop",
        "continue",
        "change",
        "accepted",
        "foreseeable_payment_date",
        "settled",
        "validation_date",
        "payment_id",
        "user_subscription_id",
        "renewal_status",
        "start_at",
        "planning_id",
        "season_id",
        "activity_id",
        "subscription_id",
        "day",
        "num_trimester",
        "establishment_id",
        "activity_session_id",
    ];

    protected $with = [
        'subscription',
        'subscription.customer',
        'establishment',
        'season',
        'activity',
        'planning',
    ];

    protected $appends = [
        'status_fr',
    ];

    public function activity()
    {
        return $this->hasOne(Activity::class, "id", "activity_id");
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, "subscription_id");
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class, "establishment_id");
    }

    public function wishes()
    {
        return $this->hasMany(Renewal::class, "subscription_id", 'subscription_id');
    }

    public function activity_session()
    {
        return $this->belongsTo(ActivitySessions::class, "activity_session_id", 'id');
    }

    public function planning()
    {
        return $this->belongsTo(Planning::class, "planning_id");
    }

    public function season()
    {
        return $this->belongsTo(Season::class, "season_id");
    }

    public function getStatusFrAttribute()
    {
        switch ($this->renewal_status) {
            case 'continue':
                return 'Continue';
                break;
            case 'not_informed':
                return 'Pas informé';
                break;
            case 'continue_change_time':
                return 'Continuer et changer horaire';
                break;
            case 'continue_change_time_else_stop':
                return 'Continuer et changer horaire sinon STOP';
                break;
            case 'stop':
                return 'STOP';
                break;
            default:
                return '...';
                break;
        }
    }

    public function scopeOrder($query)
    {
        $keys = ['updated_at', 'first_name', 'num_trimester', 'establishment_name', 'season_year_start', 'start_at'];

        return $query->when(request()->has('sortBy') && in_array(request()->sortBy, array_merge($this->fillable, $keys)), function ($query) {
            $direction = ((in_array(strtolower(request()->sortDirection), ['asc', 'desc'])) ? request()->sortDirection : 'asc');
            if (request()->get('sortBy') == 'first_name') {
                $query->leftJoin('user_subscriptions AS us_', 'us_.id', '=', 'renewals.subscription_id')
                    ->leftJoin('users AS u_', 'u_.id', '=', 'us_.user_id')
                    ->orderBy('u_.first_name', $direction);
            } else if (request()->get('sortBy') == 'establishment_name') {
                $query->leftJoin('establishments AS e_', 'e_.id', '=', 'renewals.establishment_id')
                    ->orderBy('e_.name', $direction);
            } else if (request()->get('sortBy') == 'season_year_start') {
                $query->leftJoin('seasons AS s_', 's_.id', '=', 'renewals.season_id')
                    ->orderBy('s_.date_start', $direction);
            } else {
                $query->orderBy(
                    request()->sortBy,
                    $direction
                );
            }
            $query->select('renewals.*');
        })->when(!request()->has('sortBy'), function ($query) {
            $query->orderByRaw('renewals.updated_at DESC')
                ->select('renewals.*');
        });
    }

    public function scopeFilter($query)
    {
        if (is_numeric(request()->filterBy['establishment_id'] ?? false)) {
            $query->where('establishment_id', request()->filterBy['establishment_id']);
        }

        if (is_numeric(request()->filterBy['season_id'] ?? false)) {
            $query->where('season_id', request()->filterBy['season_id']);
        }

        if (is_numeric(request()->filterBy['activity_id'] ?? false)) {
            $query->where('activity_id', request()->filterBy['activity_id']);
        }

        if (is_numeric(request()->filterBy['num_trimester'] ?? false)) {
            $query->where('num_trimester', request()->filterBy['num_trimester']);
        }

        if (!empty(request()->filterBy['renewal_status'] ?? false)) {
            $query->where('renewal_status', request()->filterBy['renewal_status']);
        }

        if (request()->get('filterBy')['renewed_subscription_saved'] ?? false === 'true') {
            $query->whereHas('subscription', function ($q) {
                $q->whereNotNull('renewal_id');
            });
        }
    }

    public function scopeSearch($query)
    {
        return $query->when(request()->q, function ($query, $q) {
            if (preg_match("/client_id/i", $q)) {
                $q_array = explode(':', $q);
                $key = $q_array[0];
                $q = $q_array[1] ?? '';

                switch ($key) {
                    case "client_id":
                        $query->WhereHas('subscription', function ($query) use ($q) {
                            $query->where('user_id', $q);
                        });
                        break;
                }
            } else {
                $query->whereRaw("(
                renewal_status LIKE '%$q%'
                )");

                $query->WhereHas('subscription.customer', function ($query) use ($q) {
                    $query->whereRaw("(
                    name LIKE '%$q%'
                    OR first_name LIKE '%$q%'
                    OR email LIKE '%$q%'
                    OR (LOWER(CONCAT(first_name, ' ', name)) LIKE LOWER('%$q%'))
                    OR (LOWER(CONCAT(name, ' ', first_name)) LIKE LOWER('%$q%'))
                        )");
                });
                $query->OrWhereHas('activity', function ($query) use ($q) {
                    $query->whereRaw("(
                    name LIKE '%$q%'
                        )");
                });
                $query->OrWhereHas('establishment', function ($query) use ($q) {
                    $query->whereRaw("(
                    name LIKE '%$q%'
                        )");
                });
            }
        });
    }
}
