<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Action;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class CustomerFileExportedNotification extends Notification
{
  use Queueable;

  protected $path;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($path)
  {
    $this->path = $path;
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
    $mail = (new MailMessage)
      ->subject('Fichier Exporté')
      ->line('Le fichier de liste de clients que vous avez exporté est maintenant disponible');

    if (is_array($this->path)) {
      foreach ($this->path as $part => $path) {
        $mail->line(new Action('Cliquez ici pour télécharger le partie ' . ($part + 1), url(Storage::url($path))));
      }
    } else {
      $mail->action('Cliquez ici pour télécharger', url(Storage::url($this->path)));
    }

    return $mail;
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
