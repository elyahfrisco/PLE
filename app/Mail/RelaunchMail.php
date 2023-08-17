<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RelaunchMail extends Mailable
{
  use Queueable, SerializesModels;

  protected $relaunch;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($relaunch)
  {
    $this->relaunch = $relaunch;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this
      ->subject($this->relaunch->subject)
      ->bcc('app-test@jprudence.com')
      ->view('mail.relaunch')->with([
        'relaunch' => $this->relaunch,
      ]);
  }
}
