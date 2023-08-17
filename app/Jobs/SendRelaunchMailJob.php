<?php

namespace App\Jobs;

use App\Mail\RelaunchMail;
use App\Models\UserRelaunch;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendRelaunchMailJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $relaunch;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($relaunch)
  {
    $this->relaunch = $relaunch;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $UserRelaunchInDb = UserRelaunch::find($this->relaunch->id);

    if ($this->relaunch && $UserRelaunchInDb && !$UserRelaunchInDb->executed) {
      try {
        $user_mail = $this->relaunch->user->email;
        if (is_local())
          $user_mail = dev_mail();

        $mail = new RelaunchMail((object) $this->relaunch);
        Mail::to($user_mail)->send($mail);
        $this->relaunch->update(['executed' => true]);
      } catch (\Throwable $th) {
      }
    }
  }
}
