<?php

use App\Actions\CleanupDatabaseEmailAction;
use Inertia\Inertia;
use App\Jobs\RunWorkJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ImportCustomer;
use App\Http\Controllers\PostController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BugController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowedUserController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\RecuperationRequestController;
use App\Http\Controllers\StatisticController;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Mail;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
  Route::middleware(['auth:sanctum', 'verified'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

  /**  -- route for account */
  Route::prefix('account')->name('account.')->group(function () {

    Route::post("/", [AccountController::class, 'store'])->name('store');

    Route::get("edit_photo/{user_id?}", [AccountController::class, 'edit_photo'])->name('photo.edit');
    Route::post("edit_photo/{user_id?}", [AccountController::class, 'update_photo'])->name('photo.update');

    Route::get("edit_medical_certificate/{user_id?}", [AccountController::class, 'edit_medical_certificate'])->name('medical_certificate.edit');
    Route::post("edit_medical_certificate/{user_id?}", [AccountController::class, 'update_medical_certificate'])->name('medical_certificate.update');

    Route::get("/edit/{user_id?}", [AccountController::class, 'edit'])->name('edit');
    Route::put("/update/{user_id?}", [AccountController::class, 'update'])->name('update');
    Route::get('/{user_id?}', [AccountController::class, 'index'])->name('index');
    Route::delete('/{user_id?}', [AccountController::class, 'destroy'])->name('destroy');
  });

  /** route for account --  */

  /**  -- absence preventions  */
  Route::prefix('absences')->name('absences.')->group(function () {
    Route::get('user/{user_id?}/create', [AbsenceController::class, 'create'])->name('absences.create');
    Route::post('user/{user_id?}/create', [AbsenceController::class, 'store'])->name('absences.store');
    Route::get('notified', [AbsenceController::class, 'notified_absences'])->name('notified');
  });
  Route::resource('absences', AbsenceController::class)
    ->only(['index', 'destroy', 'store', 'create']);

  /** absence preventions --  */

  /**  -- boutique  */
  Route::resource('subscription', SubscriptionController::class);
  /** boutique --  */

  /** --- facture */
  Route::get('facture', [SubscriptionController::class, 'facture'])->name('subscription.customer.facture');
  /** facture --- */

  /**  -- payment  */
  /** payment --  */

  /**  -- posts  */
  Route::resource('posts', PostController::class);
  /** posts --  */

  /**  -- followings  */
  Route::resource('following', FollowedUserController::class);
  Route::get('following/cookie/clear', [FollowedUserController::class, 'cookie_clear'])->name('following.cookie.clear');
  Route::get('followings/requests', [FollowedUserController::class, 'admin_index'])->name('followings.requests');
  /** followings --  */

  /**  -- invoice  */
  Route::get('invoice/{id}', [InvoiceController::class, 'show'])->name('invoice.show');
  /** invoice --  */

  /**  -- file d'attente  */
  Route::resource("queues", QueueController::class);
  /** file d'attente --  */

  /**  -- recuperation request  */
  Route::resource('recuperation_requests', RecuperationRequestController::class)->except(['create', 'edit']);
  Route::prefix('recuperation_requests')->name('recuperation_requests.')->group(function () {
    Route::get('recuperation/{id}/presence/confirm', [RecuperationRequestController::class, 'confirm_presence'])->name('presence.confirm');
    Route::get('recuperation/{id}/presence/cancel', [RecuperationRequestController::class, 'cancel_presence'])->name('cancel');
  });
  /** recuperation request --  */

  /** -- renewals  */
  Route::resource('renewals', RenewalController::class)->except('create', 'edit', 'update');
  /** renewals --  */

  /** -- payments  */
  Route::resource('payments', PaymentController::class);
  /** payments --  */

  /** -- invoices  */
  Route::get("invoices/unpaid", [BillController::class, 'unpaidInvoice'])->name('invoice.unpaid.index');
  /** invoices --  */

  /**  -- bugs  */
  Route::resource('bugs', BugController::class);
  /** bugs --  */


  /* Route list attente renouvel */
  Route::get('liste-attente', [RenewalController::class, 'attente'])->name('attente.renouvellement');

  Route::get('download-export-file/{file_name}', function ($file_name) {
    if (!Storage::exists("public/export/customer/$file_name"))
      abort(404);
    return Storage::download("public/export/customer/$file_name");
  })->name('dowload-export-file');

  /** contact  */
  Route::resource('contact', ContactController::class)->only(['store', 'destroy']);
  Route::get('contacts', [ContactController::class, 'index'])->name('contact.index');
  Route::get('contact', [ContactController::class, 'create'])->name('contact.create');
  /** contact */

  /** statistique */
  Route::get('statistics', [StatisticController::class, 'index'])->name('statistics.index');
  /** statistique */

  // route for CleanupDatabaseEmailAction
  Route::get(
    'cleanup-db-email',
    fn (CleanupDatabaseEmailAction $cleanupDatabaseEmailAction) => $cleanupDatabaseEmailAction->execute()
  );

  Route::get(
    'dirty-email-with-subscription',
    fn (CleanupDatabaseEmailAction $cleanupDatabaseEmailAction) => $cleanupDatabaseEmailAction->dirtyEmailWithSubscription()
  );

  Route::get(
    'dirty-email-no-subscription',
    fn (CleanupDatabaseEmailAction $cleanupDatabaseEmailAction) => $cleanupDatabaseEmailAction->dirtyEmailNoSubscription()
  );

  Route::get(
    'reset-child-parent-emails',
    fn (CleanupDatabaseEmailAction $cleanupDatabaseEmailAction) => $cleanupDatabaseEmailAction->resetChildParentEmails()
  );

  /* Route list */
  Route::get('routes' . date('d'), function () {
    Artisan::call('route:list --except-path=_debugbar,_ignition');
    $output = Artisan::output();
    dd($output);
  })->name('routes.list');

  Route::get('/components', function () {
    return Inertia::render('Components/index');
  })->name('components');
});

Route::get('/csrf', function () {
  return csrf_token();
});

Route::get('/', function () {
  return redirect('/login');
})->name('home');

Route::get('/home', function () {
  return redirect('/dashboard');
});

Route::get('/work', function () {
  RunWorkJob::dispatchAfterResponse();
  dd('work run');
});

Route::get('/work/{name}', function ($name) {
  Artisan::call("queue:work --queue=$name --stop-when-empty");
})->name('work.in');

Route::get('/work/{name}/one', function ($name) {
  Artisan::call("queue:work --queue=$name --once");
})->name('work.in.one');

Route::get('send-mail', function () {
  $details = [
      'title' => 'Mail from ItSolutionStuff.com',
      'body' => 'This is for testing email using smtp'
  ];

  $sent = Mail::to('dev06@kawa-group.fr')->send(new \App\Mail\MyTestMail($details));

  dd($sent);
});
