<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\CustomerFileExportedNotification;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class CustomerExportJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

  protected  $filters;
  protected  $user_id;
  protected  $list_name = "Clients";
  protected  $account_type = "customer";

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($filters, $ip, $user_id)
  {
    $this->filters = $filters;

    $this->filters['ip'] = $ip;
    $this->user_id = $user_id;

    $account_type = $this->filters['account_type'] ?? null;

    if ($account_type === 'prospect') {
      $this->list_name = "Prospects";
      $this->account_type = "prospect";
    } elseif ($account_type === 'attente') {
      $this->list_name = "List d'attente";
      $this->account_type = "attente";
    }

    $this->list_name .= '_' . date('d m Y H\hi');
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $this->changePhpIniValue();

    try {

      $file_part = 0;
      $memory_limit = $this->memory_limit;
      $max_execution_time = $this->max_execution_time;
      $account_type = $this->account_type;

      $this->exportQuery($this->filters)->chunk(intval(config('app.customer_export_chunk', 2000)), function ($users) use (
        &$file_part,
        $account_type
      ) {
        $users_id  = $users->pluck('id')->toArray();
        $users_id_count = count($users_id);

        $file_part++;
        $file_path = $this->generateFileName($file_part, $users_id_count);

        CustomerPartExportJob::dispatchSync($users_id, $file_path, $account_type);
      });
    } catch (\Exception $e) {
      info("Export error :" . $e->getMessage());
      throw $e;
    }

    ini_set('memory_limit', $memory_limit);
    ini_set('max_execution_time', $max_execution_time);
  }

  private function exportQuery($filters)
  {
    $query = User::query();

    if ($this->account_type === 'prospect') {
      $query->prospectRole();
    } elseif ($this->account_type === 'attente') {
      $query->attenteRole();
    } else {
      $query->customerRole()
        ->BirthDate(false, $filters['birthdate'] ?? null);
    }

    $query
      ->search($filters['q'] ?? null)
      ->filter($filters['filterBy'] ?? null, $filters['account_type'] ?? null)
      ->select('users.*');
    
    return $query;
  }

  private function changePhpIniValue()
  {
    $this->memory_limit = ini_get('memory_limit');
    ini_set('memory_limit', '-1');

    $this->max_execution_time = ini_get('max_execution_time');
    ini_set('max_execution_time', '-1');
  }

  private function resetPhpIniValue()
  {
    ini_set('memory_limit', $this->memory_limit);
    ini_set('max_execution_time', $this->max_execution_time);
  }

  private function generateFileName($number, $count)
  {
    return
      "public/export/customer/{$this->list_name}" . ($number ? "_part_$number" : '') . "_nbr{$count}.xlsx";
  }
}
