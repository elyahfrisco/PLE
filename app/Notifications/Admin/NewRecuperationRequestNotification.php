<?php

namespace App\Notifications\Admin;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Models\RecuperationRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewRecuperationRequestNotification extends Notification
{
  use Queueable;

  public $recuperationRequest;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(RecuperationRequest $recuperationRequest)
  {
    $this->recuperationRequest = $recuperationRequest;
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
    $sessionToCatchUp = $this->recuperationRequest->session_to_catch_up;
    $sessionToCatchUpDate = Carbon::parse($sessionToCatchUp->time_start);
    $activityName = strtoupper($sessionToCatchUp->activity->name);

    return (new MailMessage)
      ->subject('Nouvelle demande de récupération')
      ->line("Une nouvelle demande de récupération vient d'être soumise par {$this->recuperationRequest->user->full_name}, pour sa seance de {$activityName}
       du {$sessionToCatchUpDate->format('d/m/Y')} à {$sessionToCatchUpDate->format('H:i')}")
      ->action(
        'Cliquez ici pour voir la demande',
        route(
          'establishments.plannings.sessions.participants',
          [
            "establishment" => $sessionToCatchUp->establishment_id,
            "activity_session" => $this->recuperationRequest->activity_session_id_to_catch_up,
          ]
        )
      );
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }
}
