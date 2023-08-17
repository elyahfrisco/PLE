<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CleanupDatabaseEmailAction
{
  /**
   * Create the action.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Execute the action.
   *
   * @return void
   */
  public function execute()
  {
    // $this->replacePrimaryMail();
    // $this->replaceDirtyEmailToNull();

    $users = $this->dirtyUsersQuery()
      ->get();

    return $this->exportCsv($users);
  }

  public function dirtyEmailNoSubscription()
  {
    $users_query = $this->dirtyUsersQuery()
      ->leftJoin('user_subscriptions', 'user_subscriptions.user_id', '=', 'users.id')
      ->whereNull('user_subscriptions.id');

    $users_query->delete();

    dd('users deleted');

    return $this->exportCsv($users_query->withTrashed()->get());
  }

  public function dirtyEmailWithSubscription()
  {
    $users = $this->dirtyUsersQuery()
      ->join('user_subscriptions', 'user_subscriptions.user_id', '=', 'users.id')
      ->addSelect('user_subscriptions.id as subscription_id')
      ->get();

    return $this->exportCsv($users);
  }

  private function exportCsv($users)
  {
    $fileName = 'users-' . time() . '.csv';

    $headers = array(
      "Content-type"        => "text/csv",
      "Content-Disposition" => "attachment; filename=$fileName",
      "Pragma"              => "no-cache",
      "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
      "Expires"             => "0"
    );

    $columns = array('ID', 'First Name', 'Last Name', 'Email', 'Email 1', 'Email 2');

    $callback = function () use ($users, $columns) {
      $file = fopen('php://output', 'w');
      fputcsv($file, $columns);

      foreach ($users as $task) {
        $row['ID'] = $task->id;
        $row['First Name'] = $task->first_name;
        $row['Last Name'] = $task->name;
        $row['Email'] = $task->email;
        $row['Email 1'] = $task->mail1;
        $row['Email 2'] = $task->mail2;

        fputcsv(
          $file,
          array(
            $row['ID'],
            $row['First Name'],
            $row['Last Name'],
            $row['Email'],
            $row['Email 1'],
            $row['Email 2']
          ),
          ";",
        );
      }

      fclose($file);
    };

    return response()->stream($callback, 200, $headers);
  }

  public function replacePrimaryMail()
  {
    $query = $this->dirtyUsersQuery();

    $query->chunk(500, function ($users) {
      foreach ($users as $user) {
        if ($user->mail1 && filter_var($user->mail1, FILTER_VALIDATE_EMAIL)) {
          $user->email = $user->mail1;
          $user->mail1 = null;
        } else if ($user->mail2 && filter_var($user->mail2, FILTER_VALIDATE_EMAIL)) {
          $user->email = $user->mail2;
          $user->mail2 = null;
        }

        if ($user->isDirty()) {
          try {
            $user->save();
          }
          // catch Duplicate entry '...' for key 'users.email_unique'
          catch (\Illuminate\Database\QueryException $e) {
            Log::build([
              'driver' => 'single',
              'path' => storage_path('logs/duplicate user.log'),
            ])->info($user->email);
          }
        }
      }
    });
  }

  public function replaceDirtyEmailToNull()
  {
    User::where('mail1', 'www.')
      ->update(['mail1' => null]);

    User::where('mail2', 'www.')
      ->update(['mail2' => null]);
  }

  private function dirtyUsersQuery()
  {
    return User::query()
      ->where('users.is_child', false)
      ->where(
        fn ($query) =>
        $query
          ->orWhere('users.email', 'like', '%@lpde.fr')
          ->orWhere('users.email', 'like', '%@ple.fr')
          ->orWhere('users.email', 'like', '%@lpdle.fr')
          ->orWhere('users.email', 'like', '%@lpe.fr')
          ->orWhere('users.email', 'like', '%@lesplaisirsdeleau.fr')
          ->orWhere('users.email', 'like', '%@ztls.fr')
          ->orWhere('users.email', 'regexp', '^[ceCE][0-9]{1,6}@')
          ->orWhere('users.mail1', 'regexp', '^[ceCE][0-9]{1,6}@')
          ->orWhere('users.mail2', 'regexp', '^[ceCE][0-9]{1,6}@')
      )

      ->leftJoin('users as u', 'u.email', '=', 'users.mail1')
      ->leftJoin('users as u2', 'u2.email', '=', 'users.mail2')
      ->leftJoin(
        'users as u3',
        fn ($join) => $join
          ->on('u3.mail2', '=', 'users.mail1')
          ->whereNotNull('u3.mail2')
      )

      ->select(
        'users.id',
        'users.first_name',
        'users.name',
        'users.email',
        'users.mail1',
        'users.mail2',
        'u.id as u_id',
        'u2.id as u2_id',
        'u3.id as u3_id'
      )
      ->customerRole(true)
      ->groupBy('users.id')
      ->where(
        fn ($query) => $query
          ->whereNull('u.id')
          ->whereNull('u2.id')
          ->whereNull('u3.id')
      );
  }

  public function resetChildParentEmails()
  {

    $users = User::where('is_child', true)
      ->where(fn ($q) => $q->whereRaw("mail1 = ''")->orWhereNull('mail1'))
      ->where(
        fn ($query) =>
        $query
          ->orWhere('email', 'NOT LIKE', '%@lpde.fr')
          ->orWhere('email', 'NOT LIKE', '%@ple.fr')
          ->orWhere('email', 'NOT LIKE', '%@lpdle.fr')
          ->orWhere('email', 'NOT LIKE', '%@lpe.fr')
          ->orWhere('email', 'NOT LIKE', '%@lesplaisirsdeleau.fr')
          ->orWhere('email', 'NOT LIKE', '%@ztls.fr')
          ->orWhere('email', 'NOT REGEXP', '^[ceCE][0-9]{1,6}@')
      )
      ->get();


    foreach ($users as $user) {
      $user->mail1 = $user->email;
      $user->email = genere_user_mail();
      $user->save();
    }
    if ($users->count()) {
      dump($users->pluck('email')->toArray());
      dd("email reset");
    } else {
      return redirect('/');
    }
  }
}
