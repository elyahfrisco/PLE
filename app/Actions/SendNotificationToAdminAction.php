<?php

namespace App\Actions;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class SendNotificationToAdminAction
{
  protected $notification;

  /**
   * Create the action.
   *
   * @return void
   */
  public function __construct(
    Notification $notification
  ) {
    $this->notification = $notification;
  }

  /**
   * Execute the action.
   *
   * @return void
   */
  public function execute()
  {
    $adminEmails = explode(',', config('app.admin_mail'));
    $adminEmails = array_map('trim', $adminEmails);

    /* NotificationFacade::route(
      'mail',
      $adminEmails
    )->notify($this->notification); */
  }
}
