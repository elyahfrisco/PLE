<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewProspect extends Mailable
{
  use Queueable, SerializesModels;
  public $wishes;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($wishes)
  {
    $this->wishes = $wishes;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->subject('Récapitulation des activités souhaitées')
      ->from('no-reply@lesplaisirsdeleau.fr', 'Le plaisir de l\'eau')
      ->view('mail.newProspect');
  }
}
