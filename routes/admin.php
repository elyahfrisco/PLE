<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportCustomer;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\PassController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CoachController;
use App\Http\Controllers\Admin\PriceController;
use App\Http\Controllers\Admin\SeasonController;
use App\Http\Controllers\MailTemplateController;
use App\Http\Controllers\UserRelaunchController;
use App\Http\Controllers\Admin\ClosingController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PlanningController;
use App\Http\Controllers\Admin\AssistantController;
use App\Http\Controllers\Admin\PassPriceController;
use App\Http\Controllers\Admin\TrimesterController;
use App\Http\Controllers\Admin\IntervenantController;
use App\Http\Controllers\Admin\PassCategoryController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\EstablishmentController;
use App\Http\Controllers\Admin\ActivitySessionController;
use App\Http\Controllers\Admin\ActivityPassGroupController;
use App\Http\Controllers\Admin\Export\CustomerExportController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SubscriptionParameterController;
use App\Http\Controllers\SubscriptionCommentController;

Route::prefix('establishments')->name('establishments.')->group(function () {

  Route::prefix('seasons')->name('seasons.')->group(function () {
    Route::prefix('{season_id}/passes')->name('passes.')->group(function () {
      Route::post("{pass_id}", [SeasonController::class, 'attachPass'])->name('attach');
      Route::delete("{pass_id}", [SeasonController::class, 'detachPass'])->name('detach');
    });
  });

  Route::prefix('{establishment}')->group(function () {
    Route::prefix('seasons')->name('seasons.')->group(function () {
      Route::get("/", [SeasonController::class, 'index'])->name('index');
      Route::get("create", [SeasonController::class, 'create'])->name('create');
      Route::post("/", [SeasonController::class, 'store'])->name('store');

      Route::prefix('{season_id}')->group(function () {

        Route::prefix('passes')->name('passes.')->group(function () {
          Route::get("/", [SeasonController::class, 'seasonPasses'])->name('index');

          Route::prefix('{pass_id}')->group(function () {
            Route::resource("prices", PassPriceController::class)->only(['index', 'store']);
          });
        });

        Route::prefix('activities')->name('activities.')->group(function () {
          Route::get("/", [SeasonController::class, 'seasonActivities'])->name('index');
          Route::prefix('{activity_id}')->group(function () {
            Route::resource("prices", PriceController::class)->only(['index', 'store']);
          });
        });
        Route::prefix('prices')->name('prices.')->group(function () {
          /** frais d'inscription */
          Route::post("registration", [PriceController::class, 'storeRegistrationPrice'])->name('registration');
          /** endfrais d'inscription */

          /** frais de gestion */
          Route::post("management", [PriceController::class, 'storeManagementPrice'])->name('management');
          /** end frais de gestion */
        });
      });
    });

    Route::prefix('plannings')->name('plannings.')->group(function () {

      Route::prefix('sessions')->name('sessions.')->group(function () {
        Route::get("organize/all", [PlanningController::class, 'organize_all_activities_sessions'])->name('organize.all');
        Route::prefix('{activity_session}')->group(function () {
          Route::get("participants", [ActivitySessionController::class, 'participants'])->name('participants');
          Route::put("accomplished", [ActivitySessionController::class, 'setAccompished'])->name('isaccompished');
        });
      });

      Route::get("filter", [PlanningController::class, 'filter'])->name('filter');
      Route::get("{planning_id}/stop", [PlanningController::class, 'stop'])->name('stop');

      Route::resource("sessions", ActivitySessionController::class)->only(['index', 'destroy']);
    });

    Route::resource("plannings", PlanningController::class)->except(['show']);

    Route::prefix('passes')->name('passes.')->group(function () {
      Route::get("/", [EstablishmentController::class, 'establishmentPasses'])->name('index');
      Route::post("{pass_id}", [EstablishmentController::class, 'attach_pass'])->name('attach');
      Route::delete("{pass_id}", [EstablishmentController::class, 'detach_pass'])->name('detach');
    });

    Route::prefix('activities')->name('activities.')->group(function () {
      Route::get("/", [EstablishmentController::class, 'establishment_activities'])->name('index');

      Route::prefix('{activity_id}')->group(function () {
        Route::post("/", [EstablishmentController::class, 'attachActivity'])->name('attach');
        Route::post("/status/out_pass", [EstablishmentController::class, 'toggle_out_pass_activity'])->name('out_pass');
        Route::get("/status/out_pass", [EstablishmentController::class, 'toggle_out_pass_activity'])->name('out_pass');
        Route::delete("/", [EstablishmentController::class, 'detachActivity'])->name('detach');
      });
    });
  });

  Route::prefix('seasons')->name('seasons.')->group(function () {

    Route::get("{season_id}/edit", [SeasonController::class, 'edit'])->name('edit');
    Route::put("{season_id}", [SeasonController::class, 'update'])->name('update');
    Route::delete("{season_id}", [SeasonController::class, 'destroy'])->name('destroy');

    /* Seasons - Trimesters */

    Route::name('trimesters.')->group(function () {
      Route::get("{season_id}/trimesters", [TrimesterController::class, 'index'])->name('index');
      Route::get("{season_id}/trimesters/create", [TrimesterController::class, 'create'])->name('create');
      Route::post("{season_id}/trimesters", [TrimesterController::class, 'store'])->name('store');
      Route::delete("trimesters/{trimester_id}", [TrimesterController::class, 'destroy'])->name('destroy');
      Route::put("trimesters/{trimester_id}", [TrimesterController::class, 'update'])->name('update');
    });
  });
});

Route::prefix('passes')->name('passes.')->group(function () {
  Route::get("{pass_id}/activities", [PassController::class, 'activitiesPass'])->name('activities');
  Route::delete("{pass_id}/activities/{activity_id}", [PassController::class, 'detachActivity'])->name('activities.detach');

  Route::delete("activities/groups/{group}", [ActivityPassGroupController::class, 'destroy'])->name('activities.groups.delete');

  Route::prefix('{pass_id}/activities')->name('activities.')->group(function () {
    Route::resource("groups", ActivityPassGroupController::class);
    Route::prefix('{activity_id}')->group(function () {
      Route::put("groups/{group_id}/move", [ActivityPassGroupController::class, 'move_activity'])->name('groups.move');
      Route::put("groups/{group_id}/detach", [ActivityPassGroupController::class, 'detach_activity'])->name('groups.detach');
      Route::post("/", [PassController::class, 'attachActivity'])->name('attach');
      // Route::post("/group/move", [PassController::class, 'moveGroupActivity'])->name('group.move');
    });
  });

  Route::get("categories/{id}/passes", [PassCategoryController::class, 'passes'])->name('categories.passes.index');
  Route::resource("categories", PassCategoryController::class);
});

Route::prefix('plannings')->name('plannings.')->group(function () {
  Route::get("times", [PlanningController::class, '_times'])->name('times');
  Route::get("{planning_id}", [PlanningController::class, 'organize_activity_sessions'])->name('sessions.organize');
});

Route::prefix('customers')->name('customers.')->group(function () {

  Route::get("birthday/count", [CustomerController::class, '_on_birth_date_count'])->name('birthday.count');

  Route::put("{id}/activate", [CustomerController::class, 'change_account_status'])->name('status.change');

  Route::get("{id}/comments", [CommentController::class, 'index'])->name('comments');
  Route::post("comments", [CommentController::class, 'store'])->name('comment.store');
  Route::put("comments/{id}", [CommentController::class, 'update'])->name('comment.update');
  Route::delete("comments/{comment_id}", [CommentController::class, 'destroy'])->name('comment.delete');
});

Route::get("customers/export", CustomerExportController::class)->name('customers.export');
Route::resource("customers", CustomerController::class);
Route::resource("coach", CoachController::class)->only('create', 'index');
Route::resource("admins", AdminController::class)->only('create', 'index');
Route::resource("intervenants", IntervenantController::class)->only('create', 'index');
Route::resource("assistants", AssistantController::class)->only('create', 'index');

/** Assigne coach to an session */
Route::post("session/{session_id}/coachs/assign", [ActivitySessionController::class, 'setCoachs'])->name('session.coachs.assign');

Route::resource("roles", RoleController::class);

Route::get("activities/style", [ActivityController::class, '_style_css'])->name('activities.style');


Route::post("subscriptions/multiple", [SubscriptionController::class, 'store_multiple'])
  ->name('subscriptions.store.multiple');

Route::resource("subscriptions", SubscriptionController::class);
Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
  Route::resource("periodes", SubscriptionParameterController::class);
  Route::resource("{subscription}/comments", SubscriptionCommentController::class)->only('store');
  Route::resource("comments", SubscriptionCommentController::class)->only('update', 'destroy');
  Route::delete("{subscription}/subscription_activities/delete", [SubscriptionController::class, 'destroySubscriptionActivities'])->name('activities.delete');
});

Route::resource('relaunchs', UserRelaunchController::class);

Route::post("user/sessions/{session_id}/presence", [ActivitySessionController::class, 'set_presence_status'])
  ->name('user.sessions.presence');
Route::post("users/session/{activity_session_id}/presence/all", [ActivitySessionController::class, 'set_presence_status_for_all_paticipant'])
  ->name('session.participants.presence.all');

Route::resource("closings", ClosingController::class);
Route::resource("passes", PassController::class);
Route::resource("activities", ActivityController::class);
Route::post("activities/{id}", [ActivityController::class, 'update']);
Route::resource("establishments", EstablishmentController::class);
Route::resource("trimesters", TrimesterController::class);
Route::resource("colors", ColorController::class);
Route::resource("questions", QuestionController::class);

Route::delete("mail_template/{id}/delete", [MailTemplateController::class, 'destroy'])->name('mail_template.delete');


Route::get('customer/import', [ImportCustomer::class, 'index'])->name('customer.import');
Route::post('customer/import', [ImportCustomer::class, 'store_import_excel'])->name('customer.import.store');

Route::get("export", [ExportController::class, 'index'])->name('export.index');
