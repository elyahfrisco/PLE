<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StatisticController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $total = DB::selectOne('
            SELECT
                COUNT(DISTINCT user_subscriptions.id) total
            FROM
                `activity_sessions`
                LEFT JOIN `establishments`
                    ON `activity_sessions`.`establishment_id` = `establishments`.`id`
                INNER JOIN activities
                    ON activities.id = activity_sessions.`activity_id`
                INNER JOIN `subscription_activities`
                    ON subscription_activities.`activity_session_id` = activity_sessions.`id`
                INNER JOIN user_subscriptions
                    ON user_subscriptions.id = subscription_activities.`subscription_id`
                INNER JOIN passes
                    ON passes.id = user_subscriptions.`pass_id`
                INNER JOIN users
                    ON users.id = user_subscriptions.`user_id`
            WHERE `users`.`deleted_at` IS NULL

        ');
    $passes = DB::select('
            SELECT
                passes.name,
                COUNT(DISTINCT user_subscriptions.id) total
            FROM
                `activity_sessions`
                LEFT JOIN `establishments`
                    ON `activity_sessions`.`establishment_id` = `establishments`.`id`
                INNER JOIN activities
                    ON activities.id = activity_sessions.`activity_id`
                INNER JOIN `subscription_activities`
                    ON subscription_activities.`activity_session_id` = activity_sessions.`id`
                INNER JOIN user_subscriptions
                    ON user_subscriptions.id = subscription_activities.`subscription_id`
                INNER JOIN passes
                    ON passes.id = user_subscriptions.`pass_id`
                INNER JOIN users
                    ON users.id = user_subscriptions.`user_id`
            WHERE `users`.`deleted_at` IS NULL
            GROUP BY passes.name
        ');
    $activities = DB::select('
            SELECT
                activities.name,
                activities.background_color color,
                COUNT(DISTINCT user_subscriptions.id) total
            FROM
                `activity_sessions`
                LEFT JOIN `establishments`
                    ON `activity_sessions`.`establishment_id` = `establishments`.`id`
                INNER JOIN activities
                    ON activities.id = activity_sessions.`activity_id`
                INNER JOIN `subscription_activities`
                    ON subscription_activities.`activity_session_id` = activity_sessions.`id`
                INNER JOIN user_subscriptions
                    ON user_subscriptions.id = subscription_activities.`subscription_id`
                INNER JOIN passes
                    ON passes.id = user_subscriptions.`pass_id`
                INNER JOIN users
                    ON users.id = user_subscriptions.`user_id`
            WHERE `users`.`deleted_at` IS NULL
            GROUP BY activities.name

        ');
    $total = $total->total;
    return Inertia::render('Admin/Statistic/index', compact('total', 'passes', 'activities'));
  }

  public function indexDate(Request $request)
  {
    $queryTotal = '
            SELECT
                COUNT(DISTINCT user_subscriptions.id) total
            FROM
                `activity_sessions`
                LEFT JOIN `establishments`
                    ON `activity_sessions`.`establishment_id` = `establishments`.`id`
                INNER JOIN activities
                    ON activities.id = activity_sessions.`activity_id`
                INNER JOIN `subscription_activities`
                    ON subscription_activities.`activity_session_id` = activity_sessions.`id`
                INNER JOIN user_subscriptions
                    ON user_subscriptions.id = subscription_activities.`subscription_id`
                INNER JOIN passes
                    ON passes.id = user_subscriptions.`pass_id`
                INNER JOIN users
                    ON users.id = user_subscriptions.`user_id`
            WHERE `users`.`deleted_at` IS NULL

        ';
    $queryPasses = '
            SELECT
                passes.name,
                COUNT(DISTINCT user_subscriptions.id) total
            FROM
                `activity_sessions`
                LEFT JOIN `establishments`
                    ON `activity_sessions`.`establishment_id` = `establishments`.`id`
                INNER JOIN activities
                    ON activities.id = activity_sessions.`activity_id`
                INNER JOIN `subscription_activities`
                    ON subscription_activities.`activity_session_id` = activity_sessions.`id`
                INNER JOIN user_subscriptions
                    ON user_subscriptions.id = subscription_activities.`subscription_id`
                INNER JOIN passes
                    ON passes.id = user_subscriptions.`pass_id`
                INNER JOIN users
                    ON users.id = user_subscriptions.`user_id`
            WHERE `users`.`deleted_at` IS NULL
        ';
    $queryActivites = '
            SELECT
                activities.name,
                activities.background_color color,
                COUNT(DISTINCT user_subscriptions.id) total
            FROM
                `activity_sessions`
                LEFT JOIN `establishments`
                    ON `activity_sessions`.`establishment_id` = `establishments`.`id`
                INNER JOIN activities
                    ON activities.id = activity_sessions.`activity_id`
                INNER JOIN `subscription_activities`
                    ON subscription_activities.`activity_session_id` = activity_sessions.`id`
                INNER JOIN user_subscriptions
                    ON user_subscriptions.id = subscription_activities.`subscription_id`
                INNER JOIN passes
                    ON passes.id = user_subscriptions.`pass_id`
                INNER JOIN users
                    ON users.id = user_subscriptions.`user_id`
            WHERE `users`.`deleted_at` IS NULL

        ';
    if ($request->min_date) {
      $queryTotal .= ' AND user_subscriptions.`created_at` >= \'' . $request->min_date . '\'';
      $queryPasses .= ' AND user_subscriptions.`created_at` >= \'' . $request->min_date . '\'';
      $queryActivites .= ' AND user_subscriptions.`created_at` >= \'' . $request->min_date . '\'';
    }
    if ($request->max_date) {
      $queryTotal .= ' AND user_subscriptions.`created_at` <= \'' . $request->max_date . ' 23:59:59\'';
      $queryPasses .= ' AND user_subscriptions.`created_at` <= \'' . $request->max_date . ' 23:59:59\'';
      $queryActivites .= ' AND user_subscriptions.`created_at` <= \'' . $request->max_date . ' 23:59:59\'';
    }
    $queryPasses .= ' GROUP BY passes.name';
    $queryActivites .= ' GROUP BY activities.name';
    $total = DB::selectOne($queryTotal);
    $passes = DB::select($queryPasses);
    $activities = DB::select($queryActivites);
    $total = $total->total;
    return ['total' => $total, 'passes' => $passes, 'activities' => $activities];
  }
}
