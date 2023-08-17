<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewProspectRegistrationNotification extends Notification
{
  use Queueable;

  public $prospect;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(User $prospect)
  {
    $this->prospect = $prospect;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    return (new MailMessage)
      ->subject('Nouveau prospect')
      ->line("Un nouveau prospect vient de s'inscrire : {$this->prospect->full_name}")
      ->action('Cliquez ici pour consulter ses informations', route('account.index', $this->prospect->id));
  }
}
