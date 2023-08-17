<?php

namespace App\Jobs;

use App\Exports\RenewalWaitingExport;
use App\Notifications\CustomerFileExportedNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class RenewalWaitingExportJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

  public $users;
  public $ip;
  public $user;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($users, $ip, $user)
  {
    $this->users = $users;
    $this->ip = $ip;
    $this->user = $user;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $memory_limit = ini_get('memory_limit');

    ini_set('memory_limit', '-1');

    try {
      $list_name = "Liste d'attente renouvellement";

      $path = "public/export/customer/{$list_name}_" . date('d m Y H\hi') . '.xlsx';

      (new RenewalWaitingExport($this->users))->store($path);

      Cache::remember('path_customer_export_' . $this->ip, 60 * 60 * 12, function () use ($path) {
        return $path;
      });

      $this->user->notify(new CustomerFileExportedNotification($path));

      info("Export generated");
    } catch (\Exception $e) {
      forget_cache('batch_id_customer_export_' . $this->ip);
      info("Export error");
      throw $e;
    }

    forget_cache('batch_id_customer_export_' . $this->ip);

    ini_set('memory_limit', $memory_limit);
  }
}
