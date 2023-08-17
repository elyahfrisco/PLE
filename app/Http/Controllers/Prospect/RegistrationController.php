<?php

namespace App\Http\Controllers\Prospect;

use App\Models\User;
use Inertia\Inertia;
use \Mailjet\Resources;
use App\Models\Activity;
use App\Models\ContactOrigin;
use App\Models\Establishment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

use App\Http\Requests\CustomerRequest;
use Mailjet\LaravelMailjet\Facades\Mailjet;
use App\Actions\SendNotificationToAdminAction;
use App\Notifications\Admin\NewProspectRegistrationNotification;

class RegistrationController extends Controller
{
  /**
   * Show the form for registration of a new customer.
   *
   * @return \Illuminate\Http\Response
   */
  public function singup()
  {
    // $mailjet = Mailjet::getClient();

    // $lists = Mailjet::getAllLists($filters = []);
    // dd($lists);

    // $response = $mailjet->get(Resources::$Contact);

    // $body = [
    //   'FromEmail' => 'dev06@kawa-group.fr',
    //   'FromName' => getenv('MAIL_FROM_NAME'),
    //   'Recipients' => [
    //     [
    //       'Email' => "tantelyneyall@gmail.com",
    //       'Name' => "Passenger 1"
    //     ]
    //   ],
    //   'Subject' => "Your email flight plan!",
    //   'Text-part' => "Dear passenger",
    //   'Html-part' => "<h3>Dear passenger</h3><br /><p>Hello Tantely ! we need you for something.</p>"
    // ];

    // $body = [
    //   'IsExcludedFromCampaigns' => "true",
    //   'Name' => "Tantely Linux",
    //   'Email' => "tantelylinux@gmail.com"
    // ];

    // $response = $mailjet->post(Resources::$Contact, ['body' => $body]);
    // $response = $mailjet->post(Resources::$Email, ['body' => $body]);


    // $body = [
    //   // 'FromEmail' => getenv('MAIL_FROM_ADDRESS'),
    //   // 'FromName' => getenv('MAIL_FROM_NAME'),
    //   // 'TemplateID' => 4434854,
    //   // 'TemplateLanguage' => true,
    //   // 'Subject' =>  "Your email flight plan!",
    //   // 'Text-part' => "Dear passenger, welcome to Mailjet! May the delivery force be with you!",
    //   // 'Html-part' => "<h3>Dear passenger, welcome to Mailjet!</h3><br />May the delivery force be with you!",
    //   'Recipients' => [['Email' => 'tantelylinux@gmail.com']]
    // ];

    // $body = [
    //   'Messages' => [
    //       [
    //           'From' => [
    //               'Email' => getenv('MAIL_FROM_ADDRESS'),
    //               'Name' => getenv('MAIL_FROM_NAME')
    //           ],
    //           'To' => [
    //               [
    //                   'Email' => "tantelylinux@gmail.com",
    //                   'Name' => "passenger 1"
    //               ]
    //           ],
    //           'TemplateID' => 4434854,
    //           'TemplateLanguage' => true,
    //           'Subject' => "Your email flight plan!"
    //       ]
    //   ]
    // ];

    //   $body = [
    //     'FromEmail' => getenv('MAIL_FROM_ADDRESS'),
    //     'FromName' => getenv('MAIL_FROM_NAME'),
    //     'Subject' => "Your email flight plan!",
    //     'MJ-TemplateID' => 4434854,
    //     'MJ-TemplateLanguage' => true,
    //     'Recipients' => [['Email' => "tantelylinux@gmail.com"]]
    // ];

    //   $response = $mailjet->post(Resources::$Email, ['body' => $body]);
    //   dd($response);

    extract($this->form_data());
    return Inertia::render('Prospect/registration', compact('contact_origins', 'establishments', 'activities'));
  }

  private function form_data()
  {
    return [
      'activities' => Activity::orderBy('name')->get(),
      'contact_origins' => ContactOrigin::orderBy('designation')->get(),
      'establishments' => Establishment::orderBy('name')->where('relaxation_center', false)->get(),
    ];
  }

  public function store(CustomerRequest $request)
  {
    $request->offsetSet('password', Hash::make($request->password));

    $customer = User::create($request->all());
    $customer->phones()->create(['phone' => $request->phone]);
    $customer->roles()->attach(2);
    $customer->email_verified_at = now();

    $customer->save();

    Cache::forget('prospects_roles_count');

    $succes = $customer->sendEmailVerificationNotification();

    (new SendNotificationToAdminAction(
      new NewProspectRegistrationNotification($customer)
    ))->execute();

    session()->flash('success', 'Vous recevrez un email pour confirmer votre inscription');
    return redirect()->route('login');
  }
}
