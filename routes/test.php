<?php

use App\Models\Bill;
use App\Models\User;
use App\Models\Queue;
use App\Models\Payment;
use App\Models\Renewal;
use App\Models\UserFee;
use App\Models\Subscription;
use App\Models\AbsencePrevention;
use Illuminate\Support\Facades\DB;
use App\Models\RecuperationRequest;
use Illuminate\Support\Facades\App;
use App\Models\SubscriptionActivity;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\TestController;
use App\Actions\SendNotificationToAdminAction;
use App\Notifications\Admin\NewProspectRegistrationNotification;

Route::get('migrate', function () {

  Artisan::call('migrate');
  dump('Migration effectué');

  Artisan::call('config:clear');
  Artisan::call('config:cache');
  dump('Config cache');
});

Route::get('clear/cache', function () {
  Artisan::call('config:clear');
  Artisan::call('cache:clear');
  dump('Cache nettoyez');
  return back()->with('error', 'cache is cleaned');
})->name('clear.cache');

Route::get('seed', function () {
  Artisan::call('db:seed');
  dd('Seed effectué');
});

Route::get('storage/link', function () {
  Artisan::call('storage:link');
  dd('Storage link effectué');
});

// Route::get('seed/user', function () {
//     User::factory(1)->create();
//     dd('User créé');
// });

// Route::get('clear/import', function () {
//     return;
//     User::withTrashed()->where('is_imported', 1)->get()->each(function ($user) {
//         $user->phones()->delete();
//         $user->comments()->delete();
//         $user->fees()->get()->each(function ($fee) {
//             $fee->delete();
//         });
//         $user->subscriptions()->get()->each(
//             function ($subscription) {
//                 $subscription->activities()->get()->each(function ($activity) {
//                     $activity->delete();
//                 });
//                 $subscription->bill()->delete();
//                 $subscription->delete();
//             }
//         );
//         $user->absences()->get()->each(
//             function ($absence) {
//                 $absence->delete();
//             }
//         );
//         $user->forceDelete();
//         DB::table('jobs')->where('queue', 'subscribe_a_customer_to_pass')->delete();
//     });
//     return back()->with('error', 'import is cleaned');
// })->name('clear.import');

// Route::get('clear/subscriptions', function () {
//     return;
//     DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//     Queue::truncate();
//     AbsencePrevention::truncate();
//     RecuperationRequest::truncate();
//     Payment::truncate();
//     Bill::truncate();
//     SubscriptionActivity::truncate();
//     Subscription::truncate();
//     UserFee::truncate();
//     Renewal::truncate();
//     DB::statement('SET FOREIGN_KEY_CHECKS=1;');
//     return back()->with('error', 'data is cleaned');
// })->name('clear.subscriptions');

Route::get('correct_absence', function () {
  DB::statement("update `subscription_activities` set `accomplished` = NULL, `subscription_activities`.`updated_at` = '2021-08-23 15:07:15' where `absence_prevention_id` is null and `is_debited` = 0 and `session_status_txt` is null");
  return 0;
});


/**A supprimer après l'exécution */
/** Temporaire, en raison de corrections des enregistrements */

Route::get('fix', TestController::class);

Route::prefix('local/')
  ->group(function () {

    Route::get('sendmail', function () {

      if (!App::isLocal('local')) {
        return "not allowed";
      }

      $prospect = User::latest()->first();

      (new SendNotificationToAdminAction(
        new NewProspectRegistrationNotification($prospect)
      ))->execute();

      return "ok";
    });
  });
