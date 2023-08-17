<?php

namespace App\Http\Controllers\Admin;

use App\Mail\RenewalSous;
use Illuminate\Support\Facades\Mail;
use DateTime;
use stdClass;
use Carbon\Carbon;
use App\Models\Bill;
use App\Models\User;
use Inertia\Inertia;
use App\Models\UserFee;
use Illuminate\Support\Arr;
use App\Models\Subscription;
use App\Models\UserRelaunch;
use Illuminate\Http\Request;
use App\Models\Establishment;
use App\Jobs\SendRelaunchMailJob;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Models\AbsencePrevention;
use App\Models\ActivitySessions;
use App\Models\mailTemplate;
use App\Models\Pass;
use App\Models\Payment;
use App\Models\Renewal;
use App\Models\Season;
use App\Models\SubscriptionActivity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use \Mailjet\Resources;
use Mailjet\LaravelMailjet\Facades\Mailjet;

class SubscriptionController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $subscriptions = Subscription::extractDateSessions(
      Subscription::with([
        'establishment',
        'season',
        'pass',
        'customer',
        'activities',
        'payment',
        'renewal',
      ])
        ->whereHas('customer')
        ->whereHas('season')
        ->search()
        ->order()
        ->filter()
        ->paginate(30)
        ->appends(request()->query())
    );

    foreach ($subscriptions as $key => $subscription) {
      if (!$subscription->date) {
        unset($subscriptions[$key]);
      }
    }

    return Inertia::render('Admin/Subscription/index', compact('subscriptions'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $establishments = Establishment::all();
    $data = compact('establishments') + request()->all();
    return Inertia::render('Admin/Subscription/create', $this->requireData($data));
  }

  public function requireData($data)
  {
    if ($data['num_trimester'] ?? false) {
      $data['pass_type'] = 'trimester';
    }

    if ($data['pass_id'] ?? false && !request()->num_trimester) {
      $pass = Pass::findInCache($data['pass_id']);
      if ($pass && !request()->num_trimester) {
        $data['pass_type'] = $pass->PassCategory;
      }
    }

    if ($data['season_id'] ?? false) {
      $season = Season::find($data['season_id']);
      if ($season) {
        $data['establishment_id'] = $season->establishment->id;
      }
    }

    if ($data['user_id'] ?? false) {
      $user = optional(User::find($data['user_id']));
      $data['subscription_type'] = $user->is_child ? 'child' : 'adult';
    }

    return $data;
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(SubscriptionRequest $request)
  {
    $user_id = $request->user_id;
    $season_id = $request->season_id;
    $establishment_id = $request->establishment_id;

    $latestSessions = SubscriptionActivity::where('subscription_activities.user_id', $user_id)->where('subscription_activities.establishment_id', $establishment_id)->latest('time_end')->select('date')->first();
    

    $bill = Bill::create(
      [
        'amount' => 0,
        'user_id' => $user_id,
        'predictable_payment_date' => $request->predictable_payment_date,
        'payment_method' => $request->payment_method,
        'season_id' => $request->season_id,
        'establishment_id' => $request->establishment_id,
      ],
    );

    /** montant total facture */
    $bill_amount_total = 0;

    /** strore frais d'inscription */

    if ($request->not_having_paid_registration_fees === true) {
      $user_fees = UserFee::create([
        'amount' => floatval($request->amount_registration_fees),
        'type' => 'registration',
        'bill_id' => $bill->id,
        'user_id' => $user_id,
        'season_id' => $season_id,
      ]);

      $bill_amount_total += ($request->amount_registration_fees * 1);

      /** create relaunch mail */
      /** add get template for this mail */

      $mailTemplateQuery = mailTemplate::query();

      // $templateQuery->where('name', 'registration_fees');

      $mailTemplate = collect($mailTemplateQuery->first());

      $relaunch_date = now()->addDays(10);

      $data_relaunch = [
        'subject' => $mailTemplate->get('title', 'Title - Relance Frais de souscription'),
        'content' => $mailTemplate->get('content', 'Title - Relance Frais de souscription'),
        'relaunch_type' => 'registration_fees',
        'date_relaunch' => $relaunch_date,
        'user_id' => $user_id,
        'id_group' => time(),
        'season_id' => $season_id,
        // 'bill_id' => $bill->id,
      ];

      $relaunch = UserRelaunch::create($data_relaunch);
      $work = SendRelaunchMailJob::dispatch($relaunch)->delay($relaunch_date);

      // $work = SendRelaunchMailJob::dispatchSync($relaunch);

      /** end -- create relaunch mail */
    }

    /** store frais de gestion */

    if ($request->not_having_paid_management_fees === true) {
      UserFee::create([
        'amount' => floatval($request->amount_management_fees),
        'type' => 'management',
        'bill_id' => $bill->id,
        'user_id' => $user_id,
        'season_id' => $season_id,
      ]);
      $bill_amount_total += ($request->amount_management_fees * 1);
    }

    foreach ($request->subscriptionData as $key => $subscription_) {

      $date = $subscription_['data']['sessions'][0]['dateFr'];
      $time_start = $subscription_['data']['sessions'][0]['timestart'];
      $time_end = $subscription_['data']['sessions'][0]['timeend'];

      $subscriptionColumns = ['subscription_type', 'user_id', 'season_id', 'establishment_id'];

      if (auth()->user()->isAdmin() || auth()->user()->isAssistant()) {
        $subscriptionColumns[] = 'type_of_fees';
      }

      $dataOneSubscription = $request->only($subscriptionColumns);

      $pass_id = $subscription_['data']['pass_id'];

      /** default */
      $dataOneSubscription['type_of_fees'] = 'normal';

      $dataOneSubscription['pass_id'] = $pass_id;
      $dataOneSubscription['num_trimester'] = Cache::remember("pass_trimester_$pass_id", 60, fn () => Pass::findInCache($pass_id)->pass_trimester) ? $subscription_['data']['num_trimester'] : null;

      if ($subscription_['data']['pass_type'] == 'other' || $subscription_['data']['pass_type'] == 'decouvert') {
        $dataOneSubscription['number_of_sessions'] = Pass::findInCache($subscription_['data']['pass_id'])->number_sessions;
      } else {
        $dataOneSubscription['number_of_sessions'] = count($subscription_['data']['sessions']);
      }

      $establishment_id = $request->establishment_id;

      $lastSubscription = $bill->subscriptions()->create($dataOneSubscription);

      $subscription_amount_total = 0;

      $customer_activities_data = [];

      foreach ($subscription_['data']['sessions'] as $key => $session_) {

        $session = $session_['form'];

        if ($subscription_['data']['pass_type'] != 'other' || $subscription_['data']['pass_type'] == 'decouvert') {
          $activity_price = (($session_['price']['price'] ?? 0) * 1);
          $bill_amount_total += $activity_price;
          $subscription_amount_total += $activity_price;

          $session['price'] = $activity_price;
        }

        $session['time_start'] = $session_['date'] . ' ' . $session_['time_start'];
        $session['time_end'] = $session_['date'] . ' ' . $session_['time_end'];

        $customer_activities_data[] = array_merge(
          $session,
          compact(
            'user_id',
            'pass_id',
            'establishment_id',
          )
        );
      }

      $lastSubscription->activities()->createMany($customer_activities_data);

      if ($subscription_['data']['pass_type'] == 'other'  || $subscription_['data']['pass_type'] == 'decouvert') {
        $price_pass = $subscription_['data']['amount'];
        $lastSubscription->amount =  $price_pass;
        $bill_amount_total +=  $price_pass;
      } else {
        $lastSubscription->amount = $subscription_amount_total;
      }

      $lastSubscription->save();
      
      if ($request->renewal_id) {
        /** Mise à jour de la soucription en relation avec le renouvellement */
        Subscription::whereId($request->subscription_id)->update([
          'renewal_subscription_id' => $lastSubscription->id,
          'renewal_id' => $request->renewal_id,
        ]);

        Renewal::where('subscription_id', $request->subscription_id)->where('id', '!=', $request->renewal_id)->delete();

        dispatch(function () use ($user_id, $subscription_) {
          Mail::to(User::find($user_id)->email)->send(new RenewalSous($subscription_['data']['sessions']));
        })->afterResponse();

      }


      dispatch(function () use ($lastSubscription) {
        $lastSubscription->setFirstSession();
        $lastSubscription->updateDateCanCatchUpUntil();
        $lastSubscription->updateExpireDate();
      })->onQueue('subscription_jobs');
    }

    // Send email client for the information of new souscription 
    $mailjet = Mailjet::getClient();

    if (!$latestSessions && $user_id && $subscription_) { 

      $body = [
        'FromEmail' => getenv('MAIL_FROM_ADDRESS'),
        'FromName' => getenv('MAIL_FROM_NAME'),
        'Subject' =>  "Votre première séance",
        'Text-part' => "PLE Plaisirs de l'eau",
        'Html-part' => "<p>Bonjour ".ucfirst(User::find($user_id)->first_name)."</p><br />Nous nous assurons que vous ne ratiez pas une seule séance. Nous vous rappelons que votre première séance aura lieu le "
                        .$date." à "
                        .$time_start." - ".$time_end."
                        <br /><br />PLE vous remercie ! À très bientôt !",

        'Recipients' => [['Email' => User::find($user_id)->email]]
      ];

      $mailjet->post(Resources::$Email, ['body' => $body]);
    }
    // end send email

    $bill->amount = $bill_amount_total;

    if ($bill->amount == 0) {
      $dataPayment = [
        'bill_id' => $bill->id,
        'date' => now(),
        'reference' => 'PAY_FAC_' . $request->bill_id,
        'payment_method' => 'gratuit',
        'amount' => 0,
        'user_id' => $user_id,
        'admin_id' => auth()->user()->id,
      ];
      $payment = Payment::create($dataPayment);
      $payment->subscriptions()->update(['payment_id' => $payment->id]);
    }

    $bill->save();

    forget_cache('user_establishments_' . $user_id);
    forget_cache('user_activities_' . $user_id);
    forget_cache("user_{$user_id}_activity_sessions_id");

    session()->flash('success', "Souscription effectué");
    return back();
  }

  /**
   * Subscription for multi-customers in one session
   * Store multi subscription.
   */
  public function store_multiple(Request $request)
  {
    $request->validate([
      'pass_id' => 'required',
    ]);

    $establishment_id = $request->establishment_id;
    $season_id = $request->season_id;
    $pass_id = $request->pass_id;
    $subscription_type = $request->subscription_type;

    foreach ($request->customers_id as $key => $user_id) {
      $more_info_ = $request->fees_check[$user_id];
      $more_info = collect($more_info_);

      $payment_method = 'sur place';
      $predictable_payment_date = 'sur place';

      $bill = Bill::create(
        [
          'amount' => 0,
          'user_id' => $user_id,
          'predictable_payment_date' => $predictable_payment_date,
          'payment_method' => $payment_method,
          'season_id' => $season_id,
          'establishment_id' => $establishment_id,
        ],
      );

      $bill_amount_total = 0;

      if ($more_info->get('not_having_paid_registration_fees') === true) {

        $amount_registration_fees = floatval($more_info->get('amount_registration_fees', 0));

        $user_fees = UserFee::create([
          'amount' => $amount_registration_fees,
          'type' => 'registration',
          'bill_id' => $bill->id,
          'user_id' => $user_id,
          'season_id' => $season_id,
        ]);

        $bill_amount_total += $amount_registration_fees;

        /** create relaunch mail */
        /** add get template for this mail */

        $mailTemplateQuery = mailTemplate::query();

        // $templateQuery->where('name', 'registration_fees');

        $mailTemplate = collect($mailTemplateQuery->first());

        $relaunch_date = now()->addDays(10);

        $data_relaunch = [
          'subject' => $mailTemplate->get('title', 'Title - Relance Frais de souscription'),
          'content' => $mailTemplate->get('content', 'Content - Relance Frais de souscription'),
          'relaunch_type' => 'registration_fees',
          'date_relaunch' => $relaunch_date,
          'user_id' => $user_id,
          'id_group' => time(),
          'season_id' => $season_id,
          'bill_id' => $bill->id,
        ];

        $relaunch = UserRelaunch::create($data_relaunch);
        $work = SendRelaunchMailJob::dispatch($relaunch)->delay($relaunch_date);

        /** end -- create relaunch mail */
      }

      /** store frais de gestion */
      if ($more_info->get('not_having_paid_management_fees') === true) {
        $amount_management_fees = floatval($more_info->get('amount_management_fees', 0));

        UserFee::create([
          'amount' => $amount_management_fees,
          'type' => 'management',
          'bill_id' => $bill->id,
          'user_id' => $user_id,
          'season_id' => $season_id,
        ]);
        $bill_amount_total += ($amount_management_fees * 1);
      }

      $subscriptionColumns = ['subscription_type', 'user_id', 'season_id', 'establishment_id'];

      $dataOneSubscription = [
        'subscription_type' => $subscription_type,
        'user_id' => $user_id,
        'season_id' => $season_id,
        'establishment_id' => $establishment_id,
        'type_of_fees' => 'normal',
        'pass_id' => $pass_id,
        'num_trimester' => $request->num_trimester,
      ];

      /** store subscription */
      $lastSubscription = $bill->subscriptions()->create($dataOneSubscription);

      $subscription_amount_total = 0;
      $customer_activities_data = [];

      if ($request->pass_type == 'one_session') {
        $oneSession = $request->sessions[0];

        $activity_price = floatval($oneSession['price']['price'] ?? 0);
        $bill_amount_total += $activity_price;
        $subscription_amount_total += $activity_price;

        $session = [
          'time_start' => $oneSession['date'] . ' ' . $oneSession['time_start'],
          'time_end' => $oneSession['date'] . ' ' . $oneSession['time_end'],
          'date' => $oneSession['date'],
          'planning_id' => $oneSession['planning_id'],
          'user_id' => $user_id,
          'pass_id' => $pass_id,
          'establishment_id' => $establishment_id,
          'activity_session_id' => $request->activity_session_id,
          'activity_id' => $request->activity_id,
          'price' => $activity_price,
        ];

        $lastSubscription->activities()->create($session);
      } else {

        foreach ($request->sessions as $key => $oneSession) {

          $activity_price = floatval($oneSession['price']['price'] ?? 0);
          $bill_amount_total += $activity_price;
          $subscription_amount_total += $activity_price;

          $session = [
            'time_start' => $oneSession['date'] . ' ' . $oneSession['time_start'],
            'time_end' => $oneSession['date'] . ' ' . $oneSession['time_end'],
            'date' => $oneSession['date'],
            'planning_id' => $oneSession['planning_id'],
            'user_id' => $user_id,
            'pass_id' => $pass_id,
            'establishment_id' => $establishment_id,
            'activity_session_id' => $oneSession['id'],
            'activity_id' => $request->activity_id,
            'price' => $activity_price,
          ];

          $lastSubscription->activities()->create($session);
        }
      }

      $lastSubscription->amount = $subscription_amount_total;
      $lastSubscription->save();


      $bill->amount = $bill_amount_total;
      $bill->save();

      dispatch(function () use ($lastSubscription) {
        $lastSubscription->setFirstSession();
        $lastSubscription->updateDateCanCatchUpUntil();
      })->onQueue('subscription_jobs');

      forget_cache('user_establishments_' . $user_id);
      forget_cache('user_activities_' . $user_id);
      forget_cache("user_{$user_id}_activity_sessions_id");
    }

    session()->flash('success', 'les souscriptions des clients sont effectués');
    return back();
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Subscription $subscription)
  {
    if ($request->pass_type == 'other') {
      $request->validate([
        'pass_type' => 'in:other',
        'new_selected_sessions_id' => 'required',
      ]);
    } else {
      $request->validate([
        'selected_subscription_sessions_id' => 'required',
        'new_selected_sessions_id' => 'required',
      ]);
    }

    $selected_subscription_sessions_id  = $request->selected_subscription_sessions_id;
    $user_id  = $request->user_id;

    if (count($request->new_selected_sessions_id)) {

      $news_activities_data = [];

      foreach ($request->new_selected_sessions_id as $session_id) {
        $activity_sessions = ActivitySessions::find($session_id);

        $news_activities_data[] = [
          'date' => $activity_sessions->date,
          'activity_session_id' => $activity_sessions->id,
          'planning_id' => $activity_sessions->planning_id,
          'establishment_id' => $activity_sessions->establishment_id,
          'subscription_id' => $subscription->id,
          'user_id' => $subscription->user_id,
          'activity_id' => $activity_sessions->activity_id,
          'pass_id' => $subscription->pass_id,
          'time_start' => $activity_sessions->time_start,
          'time_end' => $activity_sessions->time_end,
        ];
      }

      $subscription->activities()->createMany($news_activities_data);


      if (count($selected_subscription_sessions_id)) {
        AbsencePrevention::whereIn(
          'activity_session_id',
          DB::table('subscription_activities')
            ->whereIn('id', $selected_subscription_sessions_id)
            ->select('activity_session_id')
        )
          ->where('user_id', $user_id)
          ->get()->each(function ($absence) {
            $absence->delete();
          });

        SubscriptionActivity::whereIn('subscription_activities.id', $selected_subscription_sessions_id)->delete();
      }
    }

    dispatch(function () use ($subscription) {
      $subscription->updateDateCanCatchUpUntil();
      $subscription->updateExpireDate();
    })->onQueue('subscription_jobs');

    forget_cache('user_establishments_' . $subscription->user_id);
    forget_cache('user_activities_' . $subscription->user_id);
    forget_cache("user_{$user_id}_activity_sessions_id");

    return response("la souscription a été modifié");
  }

  public function destroySubscriptionActivities(Request $request, Subscription $subscription)
  {
    $subscription_activities_id = is_array($request->subscription_activities_id) ? $request->subscription_activities_id : [$request->subscription_activities_id];

    if (!$subscription->bill->is_paid()) {
      switch ($subscription->pass->pass_category) {
        case 'other':
          break;
        case 'trimester':
          $percent = (count($subscription_activities_id) * 100) / $subscription->activities()->count();
          $percent_removed = ($subscription->amount * $percent) / 100;
          $amount_result = ($subscription->amount * (100 - $percent)) / 100;

          $subscription->amount = $amount_result;
          $subscription->bill->update(['amount' => $subscription->bill->amount - $percent_removed]);
        default:

          break;
      }
    }

    $subscription->number_sessions_deleted++;
    $subscription->save();

    $subscription_activity = SubscriptionActivity::whereIn('subscription_activities.id', $subscription_activities_id)->get();

    if (count($subscription_activity))
      $subscription_activity->each(function ($subscription_activity) {
        $subscription_activity->delete();
      });

    dispatch(function () use ($subscription) {
      $subscription->updateDateCanCatchUpUntil();
      $subscription->updateExpireDate();
    })->onQueue('subscription_jobs');

    forget_cache('user_establishments_' . $subscription->user_id);
    forget_cache('user_activities_' . $subscription->user_id);
    forget_cache("user_{$subscription->user_id}_activity_sessions_id");

    return response("Séances supprimées");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $subscription = Subscription::find($id);

    $payment = $subscription->payment()->first();

    if ($payment) {
      return back()->with('warning', "Suppression non autorisée, la facture est déjà payée");
    }

    AbsencePrevention::whereIn(
      'activity_session_id',
      $subscription
        ->activities()
        ->pluck('activity_session_id')
    )
      ->where('user_id', $subscription->user_id)->delete();

    $subscription->comments()->delete();
    $subscription->activities()->delete();
    $subscription->renewals()->delete();

    $bill = $subscription->bill;

    forget_cache('user_establishments_' . $subscription->user_id);
    forget_cache('user_activities_' . $subscription->user_id);
    forget_cache("user_{$subscription->user_id}_activity_sessions_id");

    dispatch(function () use ($subscription) {
      $subscription->updateDateCanCatchUpUntil();
    })->onQueue('subscription_jobs');

    $subscription->delete();

    if ($bill && $bill->subscriptions->count() <= 1) {
      $bill->fees()->delete();
      $bill->delete();
    }

    session()->flash('success', "Souscription supprimé");
    return back();
  }
}
