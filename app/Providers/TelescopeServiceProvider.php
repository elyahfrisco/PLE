<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    // Telescope::night();

    $this->hideSensitiveRequestDetails();

    Telescope::filter(function (IncomingEntry $entry) {

      if (!$this->allowRecord($entry)) {
        return false;
      }

      if ($this->app->environment('local')) {
        return true;
      }


      return $entry->isReportableException() ||
        $entry->isFailedRequest() ||
        $entry->isFailedJob() ||
        $entry->isScheduledTask() ||
        $entry->hasMonitoredTag();
    });
  }

  /**
   * Prevent sensitive request details from being logged by Telescope.
   *
   * @return void
   */
  protected function hideSensitiveRequestDetails()
  {
    if ($this->app->environment('local')) {
      return;
    }

    Telescope::hideRequestParameters(['_token']);

    Telescope::hideRequestHeaders([
      'cookie',
      'x-csrf-token',
      'x-xsrf-token',
    ]);
  }

  /**
   * Register the Telescope gate.
   *
   * This gate determines who can access Telescope in non-local environments.
   *
   * @return void
   */
  protected function gate()
  {
    Gate::define('viewTelescope', function ($user) {
      return in_array($user->email, [
        "info@kawa-group.fr",
      ]);
    });
  }

  private function allowRecord(IncomingEntry $entry)
  {
    if ($entry->type == 'log') {
      $message = $entry->content['message'];
      if (
        (strpos($message, 'warning') !== false && strpos($message, 'vendor\\') !== false)
        || strpos($message, 'explode()') !== false
      ) {
        return false;
      }
      info(json_decode(json_encode($entry), true));
    }
    return true;
  }
}
