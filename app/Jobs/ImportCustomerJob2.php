<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Imports\CustomerImport;
use App\Models\UserComment;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\QueryException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use stdClass;

class ImportCustomerJob2 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileName;
    protected $extension;
    protected $subscription_info;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fileName, $extension, $subscription_info)
    {
        $this->fileName = $fileName;
        $this->extension = $extension;
        $this->subscription_info = $subscription_info;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');

        $consonneMin = 'ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,i,ø';
        $consonneMaj = 'Ç,Æ,Œ,Á,É,Í,Ó,Ú,À,È,Ì,Ò,Ù,Ä,Ë,Ï,Ö,Ü,Ÿ,Â,Ê,Î,Ô,Û,Å,I,Ø';

        if (strtolower($this->extension) == 'csv') {
            $customers = Excel::toArray(new CustomerImport, $this->fileName, 'public_import',  \Maatwebsite\Excel\Excel::CSV);
        } else {
            $customers = Excel::toArray(new CustomerImport, $this->fileName, 'public_import');
        }

        $i = 1;

        foreach ($customers as $key_sheet => $sheet) {
            $more_data = new stdClass();
            Log::info("sheet $key_sheet");

            foreach ($sheet as $key_row => $row) {
                if ($row[2] == null && $row[3] == null) {
                    continue;
                }

                if (preg_match('/([0-9]{1,2}H[0-9]*)/', $row[0])) {
                    $more_data->group = $row[0];
                    continue;
                } else {
                    $more_data->full_name = trim(mb_convert_encoding($row[2], 'UTF-8', 'UTF-8'));

                    $more_data->comment = !empty_($row[3]) ? trim(mb_convert_encoding($row[3], 'UTF-8', 'UTF-8')) : '';

                    if (is_numeric($row[1])) {
                        $more_data->age = $row[1];
                    }

                    if ($row[4] ?? false) {
                        $more_data->comment .= !empty_($row[4]) ? ('\n' . trim(mb_convert_encoding($row[4], 'UTF-8', 'UTF-8'))) : '';
                    }

                    preg_match("/([A-Z$consonneMaj ]+) {1,2}(([A-Z$consonneMaj][a-z$consonneMin]* *)*)/", $more_data->full_name, $match);

                    if (!count($match)) {
                        continue;
                    }
                }

                try {
                    $name = $match[1];
                } catch (\Throwable $th) {
                    Log::error($th);
                    Log::error($row);
                    continue;
                }

                $first_name = $match[2];

                $password = rand(1000, 10000);
                $email = genere_user_mail();

                $data_ = [
                    "name" => trim($name),
                    "first_name" => trim($first_name),
                ];

                $user = User::firstOrCreate($data_, [
                    "email" => $email,
                    "email_verified_at" => now(),
                    "password" => Hash::make($password),
                    "default_password" => $password,
                    "is_imported" => true,
                    "activated" => 1,
                ]);

                /** check if new or first */
                // if (now()->diffInSeconds($user->created_at) > 5) {
                //     /** is first */
                // } else {
                //     /** is new */
                // }

                // SubscribeACustomerToPass::dispatchSync($user->id, $more_data->group, $this->subscription_info);
                SubscribeACustomerToPass::dispatch($user->id, $more_data->group, $this->subscription_info)->onQueue('subscribe_a_customer_to_pass');

                // $index_data = $this->subscription_info['season_id'] . "_" . $this->subscription_info['activity_id'] . "_" . $this->subscription_info['num_trimester'];
                // $user->more_data->$index_data = json_encode($more_data);
                // $user->save();

                Log::info("$more_data->full_name importé");

                $user->roles()->attach(2);

                if (!empty_($more_data->comment)) {
                    $user->comments()->create(
                        [
                            "content" => $more_data->comment,
                            "user_id" => $user->id,
                        ]
                    );
                }
            }
        }
        Storage::disk('public_import')->delete($this->fileName);
    }
}
