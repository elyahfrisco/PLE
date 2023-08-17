<?php

namespace App\Http\Controllers;

use App\Jobs\RenewalWaitingExportJob;
use App\Models\Renewal;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RenewalController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $renewalQuery = Renewal::search()->filter()->order();
    $renewals = $renewalQuery->paginate(page_limit());
    return Inertia::render('Renewals/index', compact('renewals'));
  }

  public function _list()
  {
    $renewalQuery = Renewal::query();
    return $renewalQuery->paginate();
  }

  public function _satus_list()
  {
    $data = [];

    $data[] = [
      'label' => "PAS INFORME",
      'value' => "not_informed",
    ];
    $data[] = [
      'label' => "Continuer",
      'value' => "continue",
    ];
    $data[] = [
      'label' => "Continuer et changer horaire",
      'value' => "continue_change_time",
    ];
    $data[] = [
      'label' => "Continuer et changer horaire sinon STOP",
      'value' => "continue_change_time_else_stop",
    ];
    $data[] = [
      'label' => "STOP",
      'value' => "stop",
    ];

    return $data;
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

    $renewal = Renewal::where('subscription_id', $request->subscription_id)->first();

    if (in_array($request->renewal_status, ['continue_change_time', 'continue_change_time_else_stop'])) {

      $id_existants = Arr::pluck(Arr::where($request->selectedSessions, function ($value, $key) {
        return is_numeric($value['renewal_id'] ?? null);
      }), 'renewal_id');

      /** suppression des voeux non sélectionnés */
      Renewal::where('subscription_id', $request->subscription_id)->whereNotIn('id', $id_existants)->delete();

      foreach ($request->selectedSessions as $key => $session) {
        /** création de nouveaux voeux sélectionnés */
        if (!isset($session['renewal_id'])) {
          Renewal::create([
            'renewal_status' => $request->renewal_status,
            'start_at' => $session['date'] . ' ' . $session['time_start'],
            'day' => $session['day'],
            'activity_id' => $session['activity_id'],
            'establishment_id' => $session['establishment_id'],
            'season_id' => $request->season_id,
            'subscription_id' => $request->subscription_id,
            'num_trimester' => $request->num_trimester,
            'planning_id' => $session['planning_id'],
            'activity_session_id' => $session['id'],
          ]);
        }
      }
      return back()->with('success', 'Renouvellement enregistré avec les vouex');
    } else if ($renewal) {
      if (in_array($renewal->renewal_status, ['continue_change_time', 'continue_change_time_else_stop'])) {
        Renewal::where('subscription_id', $request->subscription_id)->where('id', '<>', $renewal->id)->delete();
      }

      $renewal->update($request->only('renewal_status', 'start_at', 'reason', 'day', 'activity_id', 'establishment_id'));
      if (in_array($renewal->renewal_status, ['not_informed', 'stop'])) {
        $renewal->update([
          'activity_id' => NULL,
          'day' => NULL,
          'start_at' => NULL,
          'planning_id' => NULL,
        ]);
      }
    } else {
      $renewal = Renewal::create($request->all());
    }

    return back()->with('success', 'Renouvellement enregistré');
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Renewal  $renewal
   * @return \Illuminate\Http\Response
   */
  public function show(Renewal $renewal)
  {
    return $renewal;
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Renewal  $renewal
   * @return \Illuminate\Http\Response
   */
  public function destroy(Renewal $renewal)
  {
    $renewal->delete();
    return back()->with('success', "Renouvellement supprimé");
  }

  public function attente()
  {
    /*$attentes = DB::select('
            SELECT
              u.id,
              CONCAT(u.`first_name`," ",u.name) as name,
              s.id AS subscription_id,
              renewal_status,
              r.day,
              r.renewal_status,
              UPPER(a.name) as activite,
              a_s.date,
              e.name as establishment,
              r.updated_at,
              UPPER(SUBSTRING_INDEX(a_s.`search_key`," ",2)) as search_key
            FROM
              renewals r
              INNER JOIN user_subscriptions s
                ON r.`subscription_id` = s.`id`
              INNER JOIN activities a
                ON a.id = r.`activity_id`
              INNER JOIN activity_sessions a_s
                ON a_s.`id` = r.`activity_session_id`
              INNER JOIN users u
                ON u.id = s.`user_id`
              INNER JOIN establishments e
                ON e.id = s.establishment_id
            WHERE (renewal_status = \'continue_change_time\'
              OR renewal_status = \'continue_change_time_else_stop\')
            ORDER BY r.updated_at DESC

        ');*/
    $query = DB::table('renewals')
      ->selectRaw('
                    users.id,
                    users.email,
                    CONCAT(users.`first_name`," ",users.name) as name,
                    user_subscriptions.id AS subscription_id,
                    renewal_status,
                    renewals.day,
                    UPPER(activities.name) as activite,
                    activity_sessions.date,
                    establishments.name as establishment,
                    renewals.updated_at,
                    UPPER(SUBSTRING_INDEX(activity_sessions.`search_key`," ",2)) as search_key
            ')
      ->join('user_subscriptions', 'renewals.subscription_id', '=', 'user_subscriptions.id')
      ->join('activities', 'activities.id', '=', 'renewals.activity_id')
      ->join('activity_sessions', 'activity_sessions.id', '=', 'renewals.activity_session_id')
      ->join('users', 'users.id', '=', 'user_subscriptions.user_id')
      ->join('establishments', 'establishments.id', '=', 'user_subscriptions.establishment_id')
      ->where(function ($query) {
        $query->where('renewal_status', '=', 'continue_change_time')
          ->orWhere('renewal_status', '=', 'continue_change_time_else_stop');
      });
    $query->when(request()->q, function ($query, $q) {

      $query->whereRaw("(
                users.`first_name` LIKE '%$q%'
                OR users.name LIKE '%$q%'
                OR activity_sessions.`search_key` LIKE '%$q%'
                )");
    });
    if (request()->has('filterBy') && is_array(request()->filterBy)) {
      foreach (request()->filterBy as $filter => $value) {
        if (is_numeric($value)) {
          if ($filter == 'establishment_id') {
            $query->where('renewals.establishment_id', $value);
          } else if ($filter == 'activity_id') {
            $query->where('renewals.activity_id', $value);
          }
        }
        if ($filter == 'renewal_status' && !is_null($value)) {
          $query->where('renewals.renewal_status', $value);
        }
      }
    }
    $attentes = $query->orderBy('renewals.updated_at', 'desc')
      ->get();
    $users = [];
    foreach ($attentes as $attente) {
      $users[$attente->subscription_id]['name'] = $attente->name;
      $users[$attente->subscription_id]['email'] = $attente->email;
      $users[$attente->subscription_id]['customer_id'] = $attente->id;
      $users[$attente->subscription_id]['updated_at'] = $attente->updated_at;
      $users[$attente->subscription_id]['establishment'] = $attente->establishment;
      $users[$attente->subscription_id]['renewal_status'] = $attente->renewal_status;
      $users[$attente->subscription_id]['subscription_id'] = $attente->subscription_id;
      $users[$attente->subscription_id]['activities'][] = [$attente->activite, $attente->search_key];
    }
    $users = array_values($users);

    if (request()->has('export')) {
      $export_cache_name = 'batch_id_customer_export_' . request()->ip;

      if (cache()->has($export_cache_name)) {
        return back()->with('error', "Veuillez attendre que l'autre exportation soit terminée, vous pouvez voir le fichier dans le menu \"Fichier exporté\" lorsqu'il est terminé.");
      }

      $batch = Bus::batch([
        new RenewalWaitingExportJob($users, request()->ip, auth()->user()),
      ])->onQueue('export_customers')->dispatch();

      Cache::remember($export_cache_name, 60 * 5, function () use ($batch) {
        return $batch->id;
      });

      return back()->with('success', "L'export est en cours de préparation, vous pouvez visiter le menu \"Fichier exporté\" pour voir le fichier une fois l'export terminé.");
    }

    return Inertia::render('Renewals/Attente', compact('users'));
  }
}
