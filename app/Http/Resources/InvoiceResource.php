<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */



  public function toArray($request)
  {
    $tva = 20;
    $r = '<br>';

    $billInfo = [
      "id" => $this->id,
      "amount" => $this->amount,
      /** amount */
      "ht_amount" => ht_price($this->amount),
      "tva" => n_d2($tva),
      "tva_amount" => tva_price($this->amount),
      "ht_total" => ht_price($this->amount),
      "tva_total" => tva_price($this->amount),
      "ttc_total" => n_d2($this->amount),
      "net_to_pay" => n_d2($this->amount),
      /** end amount */
      "created_at" => $this->created_at,
      "updated_at" => $this->updated_at,
      "user_id" => $this->user_id,
      "predictable_payment_date" => $this->predictable_payment_date,
      "subscriptions" => $this->subscriptions,
      "payment_method" => $this->payment_method,
      "is_paied" => $this->payment ? true : false,
      "fees" => $this->fees,
      "user" => $this->user,
    ];

    foreach ($billInfo['fees'] as $key => $fees) {
      $invoice_data = [];
      /* Set invoice fees item reference && designation */
      switch ($fees->type) {
        case 'registration':
          $invoice_data['reference'] = "FI{$fees->amount}";
          $invoice_data['designation'] = "Frais d'inscription $r Saison {$fees->season->year_start} / {$fees->season->year_end}";
          break;
        case 'management':
          $invoice_data['reference'] = "FG{$fees->amount}";
          $invoice_data['designation'] = "Frais annuels de gestion $r Saison {$fees->season->year_start} / {$fees->season->year_end}";
          break;
      }

      $invoice_data['unit_price'] = ht_price($fees->amount, $tva);
      $invoice_data['ht_price'] = ht_price($fees->amount, $tva);
      $invoice_data['ttc_price'] = n_d2($fees->amount);
      $billInfo['fees'][$key]['invoice_data'] = $invoice_data;
    }

    foreach ($billInfo['subscriptions'] as $key => $subscription) {
      $invoice_data = [];

      /* Set invoice product item reference && designation */
      $sessions_activities_count = count($subscription->activities);

      $pass_name = $subscription->pass->name;
      $establishment_name = $subscription->establishment->name;

      switch ($subscription->pass->PassCategory) {
        case 'trimester':
          $num_suffix = $subscription->num_trimester == 1 ? 'er' : '°';
          $activity = strtoupper($subscription->activities[0]->activity->name ?? '');

          $date_first_session_activity = Carbon::parse($subscription->activities[0]->date ?? '')->format('d/m/Y');

          $date_last_session_activity = '_';

          if (count($subscription->activities)) {
            $last_session_activity = $subscription->activities[count($subscription->activities) - 1];
            $date_last_session_activity = Carbon::parse($last_session_activity->date)->format('d/m/Y');
          }

          /** todo : GB ??? */

          $invoice_data['reference'] = "A{$sessions_activities_count}{$subscription->num_trimester}";

          $invoice_data['designation'] = "
                        Pass {$subscription->num_trimester}{$num_suffix} T ({$sessions_activities_count} semaines consécutives, 1 séance fixe / semaine)
                        $r {$activity}
                        $r Du $date_first_session_activity au $date_last_session_activity";

          break;

        case 'one_session':
          $activity = strtoupper($subscription->activities[0]->activity->name ?? '');
          $invoice_data['reference'] = "1S{$subscription->amount}";
          $invoice_data['designation'] = "
                    1 séance à l'unité
                    $r {$activity}
                    ";
          break;

        case 'other':
          $pass_name_exploded = explode(" ", $pass_name);
          $acronym = "";

          foreach ($pass_name_exploded as $word) {
            $acronym .= $word[0];
          }

          $invoice_data['reference'] = $acronym . $establishment_name[0];

          $invoice_data['designation'] = "
                    {$pass_name} {$establishment_name}
                    ";
          break;
      }

      $invoice_data['unit_price'] = ht_price($subscription->amount, $tva);
      $invoice_data['ht_price'] = ht_price($subscription->amount, $tva);
      $invoice_data['ttc_price'] = n_d2($subscription->amount);

      $billInfo['subscriptions'][$key]['invoice_data'] = $invoice_data;

      // dd($invoice_data);

    }

    // dd($billInfo);
    return $billInfo;
  }
}
