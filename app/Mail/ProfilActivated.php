<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfilActivated extends Mailable
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
    return $this->subject('Activation du compte')->from('no-reply@lesplaisirsdeleau.fr', 'Le plaisir de l\'eau')->view('mail.profil_activated');
  }
}



