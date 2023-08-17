<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PresenceConfirmForRecuperationMail extends Mailable
{
  use Queueable, SerializesModels;

  protected $user;
  protected $session_for_catch_up;
  protected $session_to_catch_up;
  protected $recuperation_request;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($user, $session_for_catch_up, $session_to_catch_up = null, $recuperation_request = null)
  {
    $this->user = $user;
    $this->session_for_catch_up = $session_for_catch_up;
    $this->session_to_catch_up = $session_to_catch_up;
    $this->recuperation_request = $recuperation_request;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this
      ->subject('Confirmation de présence au ratrappage')
      ->bcc('app-test@jprudence.com')
      ->view('mail.presence_confirmation_for_recuperation')->with([
        'user' => $this->user,
        'session_for_catch_up' => $this->session_for_catch_up,
        'session_to_catch_up' => $this->session_to_catch_up,
        'recuperation_request' => $this->recuperation_request,
      ]);
  }
}
