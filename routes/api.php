<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Api\ActivityApiController;
use App\Http\Controllers\Api\ActivitySessionApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Api\EstablishmentApiController;
use App\Http\Controllers\Api\PassApiController;
use App\Http\Controllers\Api\PlanningApiController;
use App\Http\Controllers\Api\SeasonApiController;
use App\Http\Controllers\Api\StatisticApiController;
use App\Http\Controllers\Api\SubscriptionApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\MailTemplateController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\UserRelaunchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::name('api.')->group(function () {

  /** Establishment */
  Route::get('establishments', [EstablishmentApiController::class, 'index'])->name('establishments');
  Route::get('establishments/{establishment}/seasons', [EstablishmentApiController::class, 'seasons'])->name('establishments.seasons');
  Route::get('establishments/{establishment}/seasons/unfinished', [EstablishmentApiController::class, 'unfinishedSeasons'])->name('establishments.seasons.unfinished');
  Route::get('establishments/{establishment}/activities', [EstablishmentApiController::class, 'activities'])->name('establishments.activities');
  Route::get('establishments/{establishment}/pass', [EstablishmentApiController::class, 'passes'])->name('establishments.passes');
  Route::get('establishments/{establishment}/plannings/sessions', [ActivitySessionApiController::class, 'activitySessionsByEstablishment'])->name('establishments.plannings.sessions');
  Route::get('establishments/{establishment}/plannings/sessions/{activity_session}/participants', [ActivitySessionApiController::class, 'activitySessionsParticipants'])->name('establishments.plannings.sessions.participants');

  /** Seasons */
  Route::get('seasons/{season}/passes', [SeasonApiController::class, 'passesBySeason'])->name('seasons.passes');
  Route::get('seasons/{season?}/passes/categories', [SeasonApiController::class, 'passesCategories'])->name('seasons.passes.categories');
  Route::get('seasons/{season}/activities', [SeasonApiController::class, 'activities'])->name('seasons.activities');
  Route::get('seasons/{season}/trimesters', [SeasonApiController::class, 'trimesters'])->name('seasons.trimesters');
  Route::get('seasons/{season}/fees', [SeasonApiController::class, 'fees'])->name('seasons.fees');
  Route::get('seasons/{season}/next', [SeasonApiController::class, 'nextSeason'])->name('season.next');

  // Route::get('passes', [ApiController::class, 'passes_activities'])->name('passes.activities');
  /** Passes */
  Route::get('passes/{pass}/activities', [PassApiController::class, 'activities'])->name('passes.activities');

  /** Activities */
  Route::get('activities', [ActivityApiController::class, 'index'])->name('activities');

  /** Plannings */
  Route::get('plannings/list', [PlanningApiController::class, 'list'])->name('plannings.list');
  Route::get('plannings/passes', [SeasonApiController::class, 'passesByPlanning'])->name('plannings.passes');
  Route::get('plannings/passes/categories', [SeasonApiController::class, 'passesCategories'])->name('plannings.passes.categories');
  Route::get('plannings', [PlanningApiController::class, 'plannings'])->name('plannings');
  Route::get('plannings/sessions', [ActivitySessionApiController::class, 'planningSessions'])->name('plannings.sessions');
  Route::get('plannings/dates/disabled', [PlanningApiController::class, 'planningDisabledDate'])->name('plannings.dates.disabled');
  Route::get('plannings/season', [PlanningApiController::class, 'seasonOfPlanning'])->name('plannings.season');
  Route::get('plannings/session/trimester', [ActivitySessionApiController::class, 'trimesterOfPlanningSession'])->name('plannings.session.trimester');

  /** Post */
  Route::get('posts', [PostController::class, '_posts'])->name('posts');

  Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    /** User */
    Route::get('user/activities', [UserApiController::class, 'activities'])->name('user.activities');
    Route::get('user/passes', [UserApiController::class, 'passes'])->name('user.passes');
    Route::get('user/establishments', [UserApiController::class, 'establishments'])->name('user.establishments');
    Route::get('user/followings', [UserApiController::class, 'followings'])->name('user.followings');
    Route::get('user/fees', [UserApiController::class, 'fees'])->name('user.fees');
    Route::post('user/attente', [UserApiController::class, 'attente'])->name('user.attente');
    Route::get('user/prospects/count', [UserApiController::class, 'prospectCount'])->name('user.prospects.count');
    Route::get('search', [UserApiController::class, 'search'])->name('user.search');

    Route::get('statistic/prospects/list', [StatisticApiController::class, 'prospectList'])->name('statistic.prospects.list');
    Route::get('statistic/filter', [StatisticController::class, 'indexDate'])->name('statistic.filter');

    /** Payments */
    Route::get("payments/list", [PaymentController::class, '_list'])->name('payments.list');
    Route::get("payments/methods", [PaymentController::class, 'payments_methods'])->name('payments.methods');

    Route::get("bill/{bill_id}/detail", [BillController::class, 'bill_detail'])->name('bill.detail');

    /** Liste absences notifiés */
    Route::get("absences/notified", [AbsenceController::class, '_notified_absences'])->name('absences.notified');

    /** Liste file d'attente */
    Route::get("queue/list", [QueueController::class, '_list'])->name('queue.list');
    Route::get("queue/{queue_id}/detail", [QueueController::class, '_detail'])->name('queue.detail');

    /** Liste souscription */
    Route::get("subscriptions/list", [SubscriptionApiController::class, 'list'])->name('subscription.list');
    Route::get("subscriptions/count_recuperation", [SubscriptionApiController::class, 'countRecuperation'])->name('subscription.count_recuperation');
    Route::get("subscriptions/{id}/detail", [SubscriptionApiController::class, 'show'])->name('subscription.detail');
    Route::get("subscriptions/{id}/comments", [SubscriptionApiController::class, 'comments'])->name('subscription.comments');
    Route::post("subscriptions/verify_related_recuperation", [SubscriptionApiController::class, 'verifyRelatedRecuperation'])->name('subscription.verify_related_recuperation');

    /** email template */
    Route::get("mail/templates", [MailTemplateController::class, '_list'])->name('mail.templates.list');

    /** Relance */
    Route::get("relaunchs/list", [UserRelaunchController::class, '_list'])->name('relaunchs.list');
    Route::post("relaunchs/send", [UserRelaunchController::class, '_send'])->name('relaunchs.send_now');

    /** Invoice */
    Route::get("bills/list", [BillController::class, '_list'])->name('bills.list');

    /** Donnés utils pour form de souscription */
    Route::get('subscription/data_form', [CustomerController::class, 'form_data'])->name('subscription.form_data');

    /** Renewals */
    Route::get('renewals/list', [RenewalController::class, '_list'])->name('renewals.list');
    Route::get('renewals/status/list', [RenewalController::class, '_satus_list'])->name('renewals.status');
  });
});
