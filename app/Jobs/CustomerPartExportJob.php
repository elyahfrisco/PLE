<?php

namespace App\Jobs;

use App\Exports\CustomerExport;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CustomerPartExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $users_id;
    public $file_path;
    public $account_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $users_id, $file_path, $account_type)
    {
        $this->file_path = $file_path;
        $this->users_id = $users_id;
        $this->account_type = $account_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = $this->getUsersFromId($this->users_id);
        info('CustomerPartExportJob: ' . count($users) . ' users');

        (new CustomerExport)
            ->setData($users)
            ->setAccountType($this->account_type)
            ->store($this->file_path);

        info("Export $this->file_path generated");
    }

    private function getUsersFromId($users_id)
    {
        return User::whereIn('id', $users_id)->get();
    }
}
