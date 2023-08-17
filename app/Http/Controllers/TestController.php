<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Pass;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TestController extends Controller
{

  public function __invoke()
  {
    dispatch(
      fn () =>
      $this
        ->setSubscriptionTrimesterNumToNullWherePassNotTrimester()
        ->updateExpireDate()
        ->updateQueueDate()
    )
      ->afterResponse();

    return 'Correction en cours';
  }

  public function updateQueueDate()
  {
    Subscription::whereNotNull('num_trimester')->groupBy('user_id')->latest()->get()->each(function ($subscription) {
      info([
        'user_id' => $subscription->user_id,
        'num_trimester' => $subscription->num_trimester
      ]);

      $subscription->updateDateCanCatchUpUntil();
    });

    info('Les dernières dates de recuperation possibles sont à jour');

    return $this;
  }

  public function updateExpireDate()
  {
    $subscription_query = Subscription::whereHas('pass', function ($query) {
      $query->whereNotNull('period_validity');
    })->latest();

    $subscription_query->get()->each(function ($subscription) {
      $subscription->updateExpireDate();
    });

    info("Les dates d'expiration des pass à jour : ");
    info($subscription_query->get()->pluck('expired_at', 'id')->toArray());

    return $this;
  }

  public function setSubscriptionTrimesterNumToNullWherePassNotTrimester()
  {
    Subscription::whereNotIn('pass_id', Pass::where('pass_trimester', true)->select('id'))->update(['num_trimester' => null]);
    return $this;
  }
}
