<?php

namespace App\Models;

use App\Models\Post;
use App\Models\Role;
use App\Models\Season;
use App\Models\UserFee;
use App\Models\Activity;
use App\Models\UserWish;
use App\Models\UserPhone;
use App\Models\UserComment;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;




class User extends Authenticatable implements MustVerifyEmail
{
  use HasApiTokens;
  use HasFactory;
  use HasProfilePhoto;
  use Notifiable;
  use TwoFactorAuthenticatable;
  use SoftDeletes;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'first_name',
    'last_name',
    'birth_date',
    'gender',
    'address',
    'postal_code',
    'city',
    'contact_origin',
    'precision_contact_origin',
    'registration_promo',
    'medical_certificat_path',
    'contact_profile',
    'speciality',
    'additional_information',
    'signature_date',
    'activated',
    'status_changed_at',
    'city_birth',
    'maternity_name',
    'used_with_other_people',
    'childcare',
    'mail1',
    'mail2',
    'is_child',
    'establishment_id',
    'activity_id',
    "is_imported",
    "code_client",
    "default_password",
    "more_data",
    "email_verified_at",
    "status",
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
    'two_factor_recovery_codes',
    'two_factor_secret',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'more_data' => 'array',
  ];

  /**
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = [
    'profile_photo_url',
    'full_name',
    'full_name_min',
    'role_id',
    'status_fr',
    'renewal_status_fr'
  ];

  protected static function boot()
  {
    parent::boot();

    static::updated(function ($user) {
      if ($user->activated != $user->getOriginal('activated')) {
        Cache::forget('prospects_roles_count');
      }
      Cache::forget('user_' . $user->id . '_roles_id');
      Cache::forget('info_user_' . $user->id);
    });
  }


  public function getStatusFrAttribute()
  {
    switch ($this->status) {
      case 'waiting_customer':
        return "Liste d'attente";
      case 'customer':
        return "Client";
      case 'old_customer':
        return "Ancient client";
      default:
        return $this->status;
    }
  }

  public function getTrashedAttribute($query)
  {
    return $this->trashed();
  }

  public function getFullNameAttribute()
  {
    return $this->first_name . " " . strtoupper($this->name);
  }

  public function getFullNameMinAttribute()
  {
    return Str::limit($this->first_name, 5, '') . Str::limit($this->name, 2, '');
  }

  public function getFullName()
  {
    return $this->first_name . " " . strtoupper($this->name);
  }

  public function phones()
  {
    return $this->hasMany(UserPhone::class);
  }

  public function wishes()
  {
    return $this->hasMany(UserWish::class);
  }

  public function comments()
  {
    return $this->hasMany(UserComment::class);
  }

  public function writtenComments()
  {
    return $this->hasMany(UserComment::class, 'commentator_id');
  }

  public function activities()
  {
    return $this->belongsToMany(Activity::class, 'user_activity', 'user_id', 'activity_id');
  }

  public function activity()
  {
    return $this->belongsToMany(Activity::class, 'user_activity', 'user_id', 'activity_id')->latest();
  }

  public function activitySessions()
  {
    return $this->belongsToMany(ActivitySessions::class, 'activity_session_user', 'user_id', 'activity_session_id');
  }

  public function subscription_activities()
  {
    return $this->belongsToMany(ActivitySessions::class, 'subscription_activities', 'user_id', 'activity_session_id');
  }

  public function first_session()
  {
    return $this->hasOne(SubscriptionActivity::class, 'user_id')->orderBy('time_start');
  }

  public function scopeStatus($query, $status)
  {
    return $this->where('activated', $status);
  }

  public function scopeBirthDate($query, $defaultToday = false, $request_date  = null)
  {
    $request_date = $request_date ? $request_date : request()->birthdate;
    $date = false;
    if ($request_date) {
      $date = (new \DateTime($request_date))->format('m-d');
    } elseif ($defaultToday) {
      $date = date('m-d');
    }
    if ($date) {
      return $query->where('birth_date', 'LIKE', "%$date");
    }
    return $query;
  }

  public function scopeOrder($query)
  {
    return $query->when(request()->has('sortBy') && in_array(request()->sortBy, array_merge($this->fillable, ['created_at', 'id'])), function ($query) {
      $query->orderBy(
        request()->sortBy,
        ((in_array(strtolower(request()->sortDirection), ['asc', 'desc'])) ? request()->sortDirection : 'asc')
      );
    })->when(!request()->has('sortBy'), function ($query) {
      $query->orderByRaw('users.created_at DESC');
    });
  }

  public function bills()
  {
    return $this->hasMany(Bill::class);
  }

  public function subscriptions()
  {
    return $this->hasMany(Subscription::class);
  }

  public function roles()
  {
    return $this->belongsToMany(Role::class, 'role_user', 'user_id');
  }

  public function role()
  {
    return $this->hasMany(Role::class, 'role_user', 'user_id');
  }

  public function posts()
  {
    return $this->hasMany(Post::class);
  }

  public function isAdmin()
  {
    return $this->role_id ==  1;
  }

  public function isCustomer()
  {
    return $this->role_id == 2 && $this->activated == 1;
  }

  public function isProspect()
  {
    return $this->role_id == 2 && $this->activated == 0;
  }

  public function isAttente()
  {
    return $this->role_id == 2 && $this->activated == 2;
  }

  public function isCoach()
  {
    return $this->role_id == 3;
  }

  public function isAssistant()
  {
    return $this->role_id == 4;
  }

  public function isIntervenant()
  {
    return $this->role_id == 5;
  }

  public function absences()
  {
    return $this->hasMany(AbsencePrevention::class);
  }

  public function followings()
  {
    return $this
      ->belongsToMany(User::class, 'followed_user', 'user_follower_id', 'user_following_id')
      ->orderBy('users.first_name')
      ->withPivot(['accepted', 'acceptation_date', 'created_at', 'updated_at', 'id']);
  }


  
  public function sendEmailVerificationNotification()
  {
    $this->notify(new \App\Notifications\VerifyEmail);

  }

  public function sendPasswordResetNotification($token)
{
    $this->notify(new \App\Notifications\ResetPasswordNotification($token));
}


  public function fees()
  {
    return $this->hasMany(UserFee::class);
  }

  /** role */

  public function getRoleIdAttribute()
  {
    return
      Cache::remember('user_' . $this->id . '_roles_id', 60, function () {
        $r = $this->roles()->first(['roles.id']);
        return $r->id ?? 0;
      });
  }

  public function getRenewalStatusFrAttribute()
  {
    switch ($this->renewal_status) {
      case 'continue':
        return 'Continue';
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
        return 'Pas informé';
        break;
    }
  }

  public function scopeJoinRole($query, $role_id)
  {
    return $query
      ->whereRaw("EXISTS ( SELECT * FROM role_user ru WHERE users.id = ru.user_id AND ru.role_id = '$role_id')");
  }

  public function scopeAdminRole($query, $or = false)
  {
    return $query->joinRole(1);
    // return $query->whereHas('roles', function ($query) {
    //     $query->where('roles.id', 1);
    // });
  }

  public function scopeCustomerRole($query, $or = false)
  {
    
    if (request()->both || $or) {
      return $query->joinRole(2);
      // return $query->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
      //   ->where('role_user.role_id', '2');
    } else {

      // return $query->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
      //   ->where('role_user.role_id', '2');
      return $query
        // ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
        // ->where('role_user.role_id','2')
        ->where('activated', 1)
        ->where(function ($q) {
          $q->where('status', '!=', 'old_customer')
            ->orWhere('status', null);
        })->joinRole(2);
        
        // ;
    }
    // if (request()->both || $or) {
    //     return $query->joinRole(2);
    // } else {
    //     return $query->where('activated', 1)->joinRole(2);
    // }
  }

  public function scopeProspectRole($query, $or = false)
  {
    
    return $query->joinRole(2);
    // return $query->where('activated', 0)->whereHas('roles', function ($query) {
    //     $query->where('roles.id', 2);
    // });

    // return $query->where('users.activated', '!=', 1)
    //   ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
    //   ->where('role_user.role_id', '2');
  }

  public function scopeAttenteRole($query, $or = false)
  {
    return $query->where('activated', 2)
      ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
      ->where('role_user.role_id', '2');
  }

  public function scopeCoachRole($query, $or = false)
  {
    return $query->joinRole(3);
    // return $query->whereHas('roles', function ($query) {
    //     $query->where('roles.id', 3);
    // });
  }

  public function scopeAssistantRole($query, $or = false)
  {
    return $query->joinRole(4);
    // return $query->whereHas('roles', function ($query) {
    //     $query->where('roles.id', 4);
    // });
  }

  public function scopeIntervenantRole($query, $or = false)
  {
    return $query->joinRole(5);
    // return $query->whereHas('roles', function ($query) {
    //     $query->where('roles.id', 5);
    // });
  }

  public function scopeFilter($query, $filterBy = null, $account_type = null)
  {
    $filterBy = $filterBy ? $filterBy : request()->filterBy;
    $account_type = $account_type ? $account_type : request()->account_type;

    $filterAttrs = ['establishment_id', 'activity_id', 'pass_id', 'planning_id', 'date_debut', 'date_fin'];

    if (
      request()->has('filterBy')
      && is_array($filterBy)
    ) {
      foreach ($filterBy as $filter => $value) {
        if (in_array($filter, $filterAttrs)) {
          if (is_numeric($value)) {
            if ($filter == 'establishment_id') {
              if ($account_type == 'prospect' || $account_type == 'attente') {
                $query->whereHas('wishes', function ($query) use ($value) {
                  $query->where('user_wishes.establishment_id', $value);
                });
              } else {
                $query->whereHas('subscription_activities', function ($query) use ($value) {
                  $query->where('subscription_activities.establishment_id', $value)
                    ->whereDate('subscription_activities.date', '>=', date("Y-m-d"));
                });
              }
            } else if ($filter == 'activity_id') {
              if ($account_type == 'prospect' || $account_type == 'attente') {
                $query->whereHas('wishes', function ($query) use ($value) {
                  $query->where('user_wishes.activity_id', $value);
                });
              } else {
                $query->whereHas('subscription_activities', function ($query) use ($value) {
                  $query->where('subscription_activities.activity_id', $value)
                    ->whereDate('subscription_activities.date', '>=', date("Y-m-d"));
                });
              }
            } else if ($filter == 'pass_id') {
              $query->whereHas('subscription_activities', function ($query) use ($value) {
                $query->where('subscription_activities.pass_id', $value);
              });
            } else if ($filter == 'planning_id') {
              $query->whereHas('subscription_activities', function ($query) use ($value) {
                $query->where('subscription_activities.planning_id', $value)
                  ->where('subscription_activities.presence_status_txt', null)
                  ->whereDate('subscription_activities.date', '>=', date("Y-m-d"));
              });
            }
          }
          if ($filter == 'date_debut' && !is_null($value)) {
            $query->where('created_at', '>=', $value);
          } else if ($filter == 'date_fin' && !is_null($value)) {
            $query->where('created_at', '<=', $value . ' 23:59:00.000');
          }
        }
      }
    }
  }

  public function scopeSearch($query, $q = null)
  {
    $q = $q ? $q : request()->q;
    $query->when($q, function ($query, $q) {
      $phone_without_space = str_replace(' ', '', $q);

      $query->where("users.id", $q)
        ->orWhereRaw("(
                name LIKE '%$q%'
                OR first_name LIKE '%$q%'
                OR email LIKE '%$q%'
                OR mail1 LIKE '%$q%'
                OR mail2 LIKE '%$q%'
                OR city LIKE '%$q%'
                OR address LIKE '%$q%'
                OR postal_code LIKE '%$q%'
                OR birth_date LIKE '%$q%'
                OR (LOWER(CONCAT(first_name, ' ', name)) LIKE LOWER('%$q%'))
                OR (LOWER(CONCAT(name, ' ', first_name)) LIKE LOWER('%$q%'))
                )");

      $query->OrWhereHas('phones', function ($query) use ($q, $phone_without_space) {
        $query->whereRaw("(
                        phone LIKE '%$phone_without_space%'
                        OR phone LIKE '%$q%'
                        OR search_key LIKE '%$phone_without_space%'
                        OR search_key LIKE '%$q%'
                        )");
      });
    });
    // if(request()->has('q')){
    //     $key = request()->q;
    //     $query->whereRaw("name LIKE '%".$key."%'")
    //         ->orWhereRaw("first_name LIKE '%".$key."%'")
    //         ->orWhereRaw("concat(first_name, ' ', name) LIKE '%".$key."%' ")
    //         ->orWhereRaw("concat(name, ' ', first_name) LIKE '%".$key."%' ")
    //         ->orWhereRaw("email LIKE '%".$key."%'")
    //         ->orWhereRaw("mail1 LIKE '%".$key."%'")
    //         ->orWhereRaw("mail2 LIKE '%".$key."%'")
    //         ->orWhereRaw("birth_date LIKE '%".$key."%'")
    //         ->whereHas('phones', function ($qu) use ($key) {
    //             $qu->whereRaw("phone LIKE '%".$key."%'");
    //             $qu->orWhereRaw("search_key LIKE '%".$key."%'");
    //         });
    // }
    // return $query;
  }

  static public function findInCache($user_id)
  {
    return Cache::remember("info_user_$user_id", (60 * 5), function () use ($user_id) {
      return static::find($user_id);
    });
  }
}
