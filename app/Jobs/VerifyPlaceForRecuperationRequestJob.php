<?php

namespace App\Jobs;

use App\Mail\PresenceConfirmForRecuperationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class VerifyPlaceForRecuperationRequestJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $recuperation_request;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($recuperation_request)
  {
    $this->recuperation_request = $recuperation_request;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $session_for_catch_up = ($this->recuperation_request->session_for_catch_up()->first());
    $session_to_catch_up = ($this->recuperation_request->session_to_catch_up()->first());

    if ($session_for_catch_up->participants_count <  $session_for_catch_up->max_effective) {
      $user = $this->recuperation_request->user()->first();

      $mail = new PresenceConfirmForRecuperationMail((object) $user, $session_for_catch_up, $session_to_catch_up, $this->recuperation_request);

      $user_mail = $user->email;

      Mail::to($user_mail)->send($mail);

      // die($mail->render());
    }
  }
}
