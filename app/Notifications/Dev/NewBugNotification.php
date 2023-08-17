<?php

namespace App\Notifications\Dev;

use App\Models\Bug;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBugNotification extends Notification
{
  use Queueable;

  public $bug;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(Bug $bug)
  {
    $this->bug = $bug;
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
      ->subject('Bug :' . $this->bug->title)
      ->line('Bug signalé :')
      ->line($this->bug->content)
      ->subject('Page : ' . $this->bug->page)
      ->subject('User : ' . $this->bug->user->first_name . " " . $this->bug->user->last_name)
      ->action('Aller à la page qui bug', $this->bug->page);
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
