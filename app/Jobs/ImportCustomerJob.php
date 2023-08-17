<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Imports\CustomerImport;
use App\Models\UserPhone;
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

class ImportCustomerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileName;
    protected $extension;
    protected $type_user;
    protected $establishment_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fileName, $extension = 'csv', $type_user = "customer", $establishment_id)
    {
        $this->fileName = $fileName;
        $this->extension = $extension;
        $this->type_user = $type_user;
        $this->establishment_id = $establishment_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $path = public_path(config('app.path_import_customer')) . $this->fileName;

        $update_status = !false;

        if (strtolower($this->extension) == 'csv') {
            $customers = Excel::toCollection(new CustomerImport, $this->fileName, 'public_import',  \Maatwebsite\Excel\Excel::CSV)[0];
            // $customers = Excel::toArray(new CustomerImport, $this->fileName, 'public_import',  \Maatwebsite\Excel\Excel::CSV)[0];
        } else {
            $customers = Excel::toCollection(new CustomerImport, $this->fileName, 'public_import')[0];
        }

        $i = 1;

        if (explode(' ', $customers[0][0])[0] != 'CLIENT') {
            $customers = $customers->skip(1);
        }

        $establishment = $customers[1][0];

        $establishment_id = $establishment == 'CLIENT BX' ? 1 : 2;

        $reformed_data = [];

        foreach ($customers as $key => $customer) {

            $name = trim(!empty_($customer[7]) ? $customer[7] : $customer[3]);
            $first_name = trim($customer[8]);

            // dd($customer);

            if (strlen(trim($name . $first_name)) == 0) {
                $i++;
                continue;
            }

            $email_ = $customer[37];
            $email1 = $customer[35];
            $email2 = $customer[20];

            /** generate password */
            $password = rand(1000, 10000);

            /** --- determine le genre */
            $gender = strtolower($customer[6]);
            $gender = $gender == 'madame' ? 'female' : ($gender == 'monsieur' ? 'male' : null);
            /** determine le genre --- */

            /** --- set principal mail */
            $email = '';

            if (!empty_($email_))
                $email = $email_;
            else {
                $email = genere_user_mail();
            }
            /** set principal mail --- */


            $phones = [];

            /** -- add phone  */
            if (!empty_($customer[17]))
                $phones[] = ['phone' => $customer[17]];

            if (!empty_($customer[38]))
                $phones[] = ['phone' => $customer[38]];

            if (!empty_($customer[43]))
                $phones[] = ['phone' => $customer[43]];
            /** add phone -- */

            $cp = empty_($customer[13]) ? '00000' : $customer[13];

            switch ($this->type_user) {
                case 'prospect':
                    $activated = 0;
                    break;
                default:
                    $activated = 1;
                    break;
            }

            $data_ = [
                "code_client" => $customer[1],
                "name" => $name,
                "first_name" => $first_name,
                "gender" => $gender,
                "address" => $customer[9],
                "postal_code" => $cp,
                "city" => $customer[14],
                "email" => $email,
                "mail1" => $email1,
                "mail2" => $email2,
                "email_verified_at" => now(),
                "password" => Hash::make($password),
                "default_password" => $password,
                "establishment_id" => $establishment_id,
                "is_imported" => true,
                "phones" => $phones,
                "activated" => $activated,
                "status" => $this->type_user,
                "establishment_id" => $this->establishment_id,
            ];

            // $reformed_data[] = $data_;
            // dd($data_);

            $data_ = collect($data_);

            if (!empty_($data_['code_client'])) {
                $user = User::where([
                    "code_client" => $data_['code_client'],
                ])->first();

                if ($user) {
                    if ($update_status) {
                        $user->update($data_->only('status')->toArray());
                    } else {
                        $user->update($data_->except('phones', 'email', 'password', 'default_password')->toArray());
                    }
                } else {
                    try {
                        $user = User::create($data_->except('phones')->toArray());
                    } catch (QueryException $e) {
                        if (strpos($e->getMessage(), 'Duplicate entry')) {

                            /** --- changer l'email */
                            if (empty_($data_["mail1"]) || $data_["mail1"] == "www.") {
                                $data_["mail1"] = $data_["email"];
                            } elseif (empty_($data_["mail2"])) {
                                $data_["mail2"] = $data_["email"];
                            } else {
                                $data_["mail1"] = $data_["email"];
                            }
                            /** changer l'email --- */

                            $data_["email"] = time();
                            $user = User::create($data_->except('phones')->toArray());
                            $user->email = default_mail($user->id);
                            $user->save();
                        } else {
                            dump($e);
                        }
                    }
                }
            } else {
                $user = User::where([
                    "name" => $name,
                    "first_name" => $first_name,
                ])->first();
                if ($user) {
                    if ($update_status) {
                        $user->update($data_->only('status')->toArray());
                    } else {
                        $user->update($data_->except('phones', 'email', 'password', 'default_password')->toArray());
                    }
                } else {
                    try {
                        $user = User::create($data_->except('phones')->toArray());
                    } catch (QueryException $e) {
                        if (strpos($e->getMessage(), 'Duplicate entry')) {
                            /** --- changer l'email */
                            if (empty_($data_["mail1"]) || $data_["mail1"] == "www.") {
                                $data_["mail1"] = $data_["email"];
                            } elseif (empty_($data_["mail2"])) {
                                $data_["mail2"] = $data_["email"];
                            } else {
                                $data_["mail1"] = $data_["email"];
                            }
                            /** changer l'email --- */

                            $data_["email"] = time();
                            $user = User::create($data_->except('phones')->toArray());
                            $user->email = default_mail($user->id);
                            $user->save();
                        } else {
                            dump($e);
                        }
                    }
                }
            }

            if ($data_['phones']) {
                foreach ($data_['phones'] as $key => $phone) {
                    if (!UserPhone::where('phone', $phone['phone'])
                        ->where('user_id', $user->id)->first()) {
                        $user->phones()->create($phone);
                    }
                }
            }

            $user->roles()->attach(2);
            Log::info("$user->full_name importé");
            $i++;
        }
        Storage::disk('public_import')->delete($this->fileName);
    }
}
