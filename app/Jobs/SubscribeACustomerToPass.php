<?php

namespace App\Jobs;

use App\Models\ActivitySessions;
use App\Models\Bill;
use App\Models\Pass;
use App\Models\Payment;
use App\Models\Planning;
use App\Models\UserFee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscribeACustomerToPass implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $user_id;
  protected $group;
  protected $subscription_info;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($user_id, $group, $subscription_info)
  {
    $this->user_id = $user_id;
    $this->group = $group;
    $this->subscription_info = $subscription_info;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    /** get planning id */
    $group_ = explode(' ', $this->group);

    /** get day */
    $day = dEngFrSigle($group_[0]);

    /** get time */
    $time = preg_replace("/(\d+)H(\d+)\D?/", '$1:$2', $group_[1]);

    $establishment_id = $this->subscription_info['establishment_id'];

    $planning = Planning::where('time_start', 'LIKE', "%$time%")
      ->where(
        [
          'day' => $day,
          'establishment_id' => $establishment_id,
          'season_id' => $this->subscription_info['season_id'],
          'activity_id' => $this->subscription_info['activity_id'],
        ]
      )
      ->wTrimester($this->subscription_info['season_id'], $this->subscription_info['num_trimester'])
      ->first();

    if ($planning) {

      $planning_id = $planning->id;
      $activity_id = $planning->activity_id;
      $sessions = ActivitySessions::where('planning_id', $planning_id)
        ->WTrimester($this->subscription_info['season_id'], $this->subscription_info['num_trimester'])
        ->get();

      $pass_id = Pass::where('pass_trimester', true)->first()->id;

      $user_id = $this->user_id;
      $season_id = $this->subscription_info['season_id'];

      $bill = Bill::create(
        [
          'amount' => 0,
          'user_id' => $user_id,
          'predictable_payment_date' => now(),
          'payment_method' => 'sur place',
          'season_id' => $season_id,
          'establishment_id' => $establishment_id,
          "is_imported" => true,
        ],
      );

      $payment = Payment::create([
        "date" => now(),
        "amount" => 0,
        "bill_id" => $bill->id,
        "user_id" => $user_id,
        "payment_method" => 'sur place',
        "reference" => 'PAY_FAC_' . $bill->id,
      ]);

      /** strore frais d'inscription */
      $user_fees = UserFee::create([
        'amount' => 0,
        'type' => 'registration',
        'bill_id' => $bill->id,
        'user_id' => $user_id,
        'season_id' => $season_id,
      ]);

      /** store frais de gestion */
      UserFee::create([
        'amount' => 0,
        'type' => 'management',
        'bill_id' => $bill->id,
        'user_id' => $user_id,
        'season_id' => $season_id,
      ]);

      $lastSubscription = $bill->subscriptions()->create([
        'type_of_fees' => 'normal',
        'subscription_type' => 'adult',
        'user_id' => $user_id,
        'pass_id' => $pass_id,
        'season_id' => $season_id,
        'payment_id' => $payment->id,
        'establishment_id' => $establishment_id,
        'bill_id' => $bill->id,
        'num_trimester' => $this->subscription_info['num_trimester'],
      ]);

      $customer_activities_data = [];

      foreach ($sessions as $key => $session) {

        $customer_activities_data[] = array_merge(
          [
            'activity_session_id' => $session['id'],
            'date' => $session['date'],
            'planning_id' => $session['planning_id'],
            'time_start' => $session['time_start'],
            'time_end' => $session['time_end'],
            'subscription_id' => $lastSubscription->id,
          ],
          compact(
            'user_id',
            'pass_id',
            'establishment_id',
            'activity_id',
            'planning_id',
          )
        );
      }
      $lastSubscription->activities()->createMany($customer_activities_data);
      Log::info("Souscription user:$user_id effectué");
    }
  }
}
