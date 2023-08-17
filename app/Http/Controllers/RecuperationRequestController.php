<?php

namespace App\Http\Controllers;

use App\Models\User;
use \Mailjet\Resources;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\RecuperationRequest;
use App\Models\SubscriptionActivity;

use Mailjet\LaravelMailjet\Facades\Mailjet;
use App\Actions\SendNotificationToAdminAction;
use App\Jobs\VerifyPlaceForRecuperationRequestJob;
use App\Notifications\Admin\NewRecuperationRequestNotification;

class RecuperationRequestController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $recuperationRequest = RecuperationRequest::create($request->all());
    // VerifyPlaceForRecuperationRequestJob::dispatchSync($recuperationRequest)->onQueue('verify_place_for_recuperation_request');

    (new SendNotificationToAdminAction(
      new NewRecuperationRequestNotification($recuperationRequest)
    ))->execute();

    VerifyPlaceForRecuperationRequestJob::dispatchAfterResponse($recuperationRequest);
    return back()->with('success', "La demande a été soumise, un email sera envoyé dès qu'une place se libère");
  }

  /**
   * Store presence confirmation.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function confirm_presence($id)
  {
    $recuperation_request = RecuperationRequest::find($id);

    SubscriptionActivity::where([
      'activity_session_id' => $recuperation_request->activity_session_id_to_catch_up,
      'user_id' => $recuperation_request->user_id,
    ])->update([
      'session_status_txt' => 'presence_for_recuperation_confirmed'
    ]);

    $recuperation_request->presence_confirmed_at = now();
    $recuperation_request->save();


    /** auto subscription */
    /** check if subscriptioon exist */

    $subscription = SubscriptionActivity::where('activity_session_id', $recuperation_request->activity_session_id_for_catch_up)
      ->where('user_id', $recuperation_request->user_id)->first();

    if (!$subscription) {

      $subscription_of_activity_prevented = SubscriptionActivity::where('activity_session_id', $recuperation_request->activity_session_id_to_catch_up)
        ->where('user_id', $recuperation_request->user_id)->first();

      $subscription_of_session_to_catch_up = SubscriptionActivity::where('activity_session_id', $recuperation_request->activity_session_id_to_catch_up)
        ->where('user_id', $recuperation_request->user_id)->first();

      $session = $recuperation_request->session_for_catch_up;

      $date_absence = date("d/m/Y", strtotime($subscription_of_activity_prevented->date)) . " à (" . date("h:m", strtotime($subscription_of_activity_prevented->time_start)) . " - " . date("h:m", strtotime($subscription_of_activity_prevented->time_end)) . ")";
      $date_recuperation = date("d/m/Y", strtotime($session->date)) . " à (" . date("h:m", strtotime($session->time_start)) . " - " . date("h:m", strtotime($session->time_end)) . ")";

      $data = [
        'date' => $session->date,
        'planning_id' => $session->planning_id,
        'establishment_id' => $session->establishment_id,
        'subscription_id' => $subscription_of_activity_prevented->subscription_id,
        'user_id' => $recuperation_request->user_id,
        'activity_id' => $session->activity_id,
        'pass_id' => $subscription_of_session_to_catch_up->pass_id,
        'activity_session_id' => $session->id,
        'is_recuperation' => true,
        'time_start' => $session->time_start,
        'time_end' => $session->time_end,
      ];

      SubscriptionActivity::create($data);

      // Send Email Notification
      $mailjet = Mailjet::getClient();

      $body = [
        'FromEmail' => getenv('MAIL_FROM_ADDRESS'),
        'FromName' => getenv('MAIL_FROM_NAME'),
        'Subject' =>  "Présence à une récupération",
        'Text-part' => "PLE Plaisirs de l'eau",
        'Html-part' => "<p>Bonjour " . ucfirst(User::find($recuperation_request->user_id)->first_name) . ",</p>
                        <br />Nous souhaitons confirmer la récupération de votre absence. Nous vous prions de  la valider :<br />
                        - La date d’absence prévue: " . $date_absence . " <br />
                        - La date de récupération à valider " . $date_recuperation . ":
                        <br />
                        L'EQUIPE DE LES PLAISIRE DE L'EAU  vous remercie ! À très bientôt ! ",

        'Recipients' => [['Email' => User::find($recuperation_request->user_id)->email]]
      ];

      $mailjet->post(Resources::$Email, ['body' => $body]);
      // End Send Email NOtification


    } else {
      if (request()->has('admin') || request()->has('redirect_back')) {
        return back()->with('info', 'le client est déjà inscrit à cette sèance');
      } else {
        return redirect()->route('customer.plannings.customer')->with('info', 'Vous êtes déjà inscrit à cette sèance');
      }
    }

    if (request()->has('admin') || request()->has('redirect_back')) {
      return back()->with('info', 'La présence pour le rattrapage a été confirmée');
    } else {
      return redirect()->route('customer.plannings.customer')->with('success', 'Votre présence pour le rattrapage a été confirmée');
    }
  }

  /**
   * Store presence confirmation.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function cancel_presence($id)
  {
    $recuperation_request = RecuperationRequest::find($id);
    // $recuperation_request->session_to_catch_up->recuperation_request_id
    $recuperation_request->forceDelete();

    if (request()->has('redirect_back')) {
      return back()->with('request_canceled', true);
    } elseif (auth()->user()->isAdmin() || auth()->user()->isAssistant()) {
      return redirect()->route('queues.index')->with('request_canceled', true);
    } else {
      return redirect()->route('customer.plannings.customer')->with('request_canceled', true);
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, RecuperationRequest $recuperation_request)
  {
    $recuperation_request = $recuperation_request->update([
      'presence_confirmed_at' => NULL,
      'generate_automatically' => false,
      'content' => $request->content,
      'activity_session_id_for_catch_up' => $request->activity_session_id_for_catch_up,
    ]);

    // VerifyPlaceForRecuperationRequestJob::dispatchSync($recuperation_request)->onQueue('verify_place_for_recuperation_request');
    VerifyPlaceForRecuperationRequestJob::dispatchAfterResponse($recuperation_request);
    return back()->with('success', "La demande a été soumise, un email sera envoyé dès qu'une place se libère");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
