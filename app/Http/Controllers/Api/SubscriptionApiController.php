<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecuperationRequest;
use App\Models\Subscription;
use App\Models\SubscriptionComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionApiController extends Controller
{
  protected $relatedTableToGet = ['establishment', 'season', 'pass', 'customer', 'activities',  'payment', 'renewal', 'bill'];

  public function list(Request $request)
  {
    $subscriptionsQuery = Subscription::with(array_merge($this->relatedTableToGet, $request->get('and_with', [])));

    if ($request->user_id) {
      $subscriptionsQuery->where('user_id', $request->user_id);
    }

    $subscriptionsQuery->orderByDesc('created_at');
    $subscriptions = Subscription::extractDateSessions(
      $subscriptionsQuery->paginate($request->per_page ?: 50)
    );

    foreach ($subscriptions as $key => $subscription) {
      if (!$subscription->date) {
        unset($subscriptions[$key]);
      }
    }

    return $subscriptions;
  }

  public function countRecuperation(Request $request)
  {
    $subscriptionsQuery = Subscription::with(array_merge($this->relatedTableToGet, $request->get('and_with', [])))
      ->whereValueNotNullAndEqualTo('user_id', $request->user_id)
      ->orderByDesc('created_at');

    $subscriptions = Subscription::extractDateSessions(
      $subscriptionsQuery->paginate(100)
    );

    $recuperation = 0;
    $absence = 0;

    foreach ($subscriptions as $key => $subscription) {
      if (!$subscription->date) {
        unset($subscriptions[$key]);
      }
    }

    foreach ($subscriptions as $key => $subscription_) {
      try {
        foreach ($subscription_->activities as $activity_key => $activity_) {
          if ($activity_->absence_prevention_id) {
            $absence++;
          }
          if ($activity_->is_recuperation === 1) {
            $recuperation++;
          }
        }
      } catch (\Throwable $th) {
      }
    }

    return $absence - $recuperation;
  }

  public function show(Request $request, $id)
  {
    try {
      if ($request->only) {
        return Subscription::extractDateSessions(Subscription::whereId($id)
          ->with($request->only)
          ->get())[0];
      } else {
        return Subscription::extractDateSessions(Subscription::whereId($id)
          ->with(
            array_merge(
              [
                'renewal',
                'establishment',
                'season',
                'pass',
                'customer',
                'activities',
                'comments'
              ],
              $request->get('and_with', [])
            )
          )
          ->when($request->with_renewal_wishes, function ($q) {
            $q->with('renewal.wishes')
              ->with('renewal.wishes.activity_session', 'renewal.wishes.activity_session.participantsNoRelation');
          })
          ->get())[0];
      }
    } catch (\Throwable $th) {
    }
  }

  public function comments(Request $request, $id)
  {
    return SubscriptionComment::whereUserSubscriptionId($id)->latest()
      ->get();
  }

  public function verifyRelatedRecuperation(Request $request)
  {
    $recuperation_count = RecuperationRequest::whereIn(
      'activity_session_id_to_catch_up',
      DB::table('subscription_activities')->whereIn('subscription_activities.id', $request->subscription_activities_id)
        ->select('subscription_activities.activity_session_id')
    )->count();

    $result = $recuperation_count ? true : false;

    return [
      'result' => $result,
      'message' => $recuperation_count == 1 ? "Une récupération est déjà demandée pour cette session " : (count($request->subscription_activities_id) > 1 ? "$recuperation_count récupérations sont déjà demandées sur les " . count($request->subscription_activities_id) . " séances selectionnées, vous ne pouvez plus les modifier" : ''),
    ];
  }
}
