<?php

namespace App\Http\Controllers\Admin;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Mail\ProfilActivated;
use App\Models\ContactOrigin;
use App\Models\Establishment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Jobs\CustomerExportJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

use \Mailjet\Resources;
use Mailjet\LaravelMailjet\Facades\Mailjet;

class CustomerController extends Controller
{

  protected $days = [
    ['en' => 'monday', 'fr' => 'lundi'],
    ['en' => 'tuesday', 'fr' => 'mardi'],
    ['en' => 'wednesday', 'fr' => 'mercredi'],
    ['en' => 'thursday', 'fr' => 'jeudi'],
    ['en' => 'friday', 'fr' => 'vendredi'],
    ['en' => 'saturday', 'fr' => 'samedi'],
    ['en' => 'sunday', 'fr' => 'dimanche']
  ];

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $has_account_type = request()->has('account_type');
    $account_type = request()->get('account_type');

    if ($has_account_type && request()->account_type == 'prospect') {
      $title = "Liste prospect - liste d'attente";
      $customers = User::prospectRole();
    } elseif (($has_account_type && request()->account_type == 'attente')) {
      $title = "Liste prospect - liste d'attente";
      $customers = User::attenteRole();
    } else {
      $title = "Liste client";
      $customers = User::BirthDate(false)->customerRole();
      $account_type = "customer";
    }

    /** à revoir */
    /*if(!empty(request()->q)){
            $key = request()->q;
            $customers->where("users.id",$key)
                ->orWhereRaw("name LIKE '%".$key."%'")
                ->orWhereRaw("postal_code LIKE '%".$key."%'")
                ->orWhereRaw("first_name LIKE '%".$key."%'")
                ->orWhereRaw("concat(first_name, ' ', name) LIKE '%".$key."%' ")
                ->orWhereRaw("concat(name, ' ', first_name) LIKE '%".$key."%' ")
                ->orWhereRaw("email LIKE '%".$key."%'")
                ->orWhereRaw("address LIKE '%".$key."%'")
                ->orWhereRaw("city LIKE '%".$key."%'")
                ->orWhereRaw("mail1 LIKE '%".$key."%'")
                ->orWhereRaw("mail2 LIKE '%".$key."%'")
                ->orWhereRaw("birth_date LIKE '%".$key."%'");
            $customers->whereHas('phones', function (Builder $q) use($key) {
                $q->whereRaw("phone LIKE '%".$key."%' ");
                $q->orWhereRaw("search_key LIKE '%".$key."%' ");
            });
            $query->search();
        }*/
    $customers = $customers
      ->search()
      ->order()
      ->filter()
      ->selectRaw('users.id, users.id as user_id, users.name, users.first_name, users.birth_date, users.profile_photo_path, users.activated, users.status, users.created_at')
      ->paginate(page_limit())
      ->appends(request()->query());

    if (request()->birthdate) {
      $title .= " <small>Date anniversaire: " . Carbon::parse(request()->birthdate)->format('l/m') . '</small>';
    }

    return Inertia::render('Account/Customer/index', compact('customers', 'title', 'account_type'));
  }

  public function _on_birth_date_count()
  {
    if (request()->birthdate) {
      $date = (new DateTime(request()->birthdate))->format('m-d');
    } else {
      $date = date('m-d');
    }
    $customers = User::withTrashed()->orderBy('deleted_at', 'ASC')->BirthDate(true)->count();
    echo $customers;
    die();
  }

  /**
   * Show the form for creating a new customer.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    extract($this->form_data());
    $user_to_duplicate = request()->duplicat_user_id ? User::find(request()->duplicat_user_id)->load('phones') : null;
    return Inertia::render('Account/Customer/create', compact(
      'establishments',
      'contact_origins',
      'activities',
      'days',
      'account_type',
      'create_for_subscription',
      'user_to_duplicate',
    ));
  }

  public function form_data()
  {
    return [
      'days' => $this->days,
      'activities' => Activity::orderBy('name')->get(),
      'contact_origins' => ContactOrigin::orderBy('designation')->get(),
      'establishments' => Establishment::orderBy('name')->where('relaxation_center', false)->get(),
      'account_type' => request()->has('account_type') ? request()->get('account_type') : 'customer',
      'create_for_subscription' => request()->create_for_subscription,
    ];
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    extract($this->form_data());
    $user = User::withTrashed()->with(['phones', 'activity', 'wishes'])->find($id);
    $account_type = !$user->activated ? 'prospect' : 'customer';
    return Inertia::render('Account/edit', compact('user', 'contact_origins', 'establishments', 'activities', 'account_type'));
  }

  /*Activate user account */
  public function change_account_status(Request $request, $customer_id)
  {
    $customer = User::find($customer_id);

    if ($request->activated) {
      if ($customer->status == 'old_customer') {
        $customer->update([
          'status' => 'customer',
          'status_changed_at' => now(),
        ]);

      } else {
        $customer->update([
          'activated' => $request->activated,
          'status_changed_at' => now(),
        ]);

        dispatch(function () use ($customer, $request) {
          Mail::to($customer->email)->send(new ProfilActivated($request->wishes));
        })->afterResponse();

        $data_wish = '';

        if ($request->wishes) {
          foreach ($request->wishes as $key => $wish) {
            $dayfr = ucfirst($wish['day']);
            switch ($wish['day']) {
              case 'monday':
                $dayfr = 'Lundi';
                break;
              case 'tuesday':
                $dayfr = 'Mardi';
                break;
              case 'wednesday':
                $dayfr = 'Mercredi';
                break;
              case 'thursday':
                $dayfr = 'Jeudi';
                break;
              case 'friday':
                $dayfr = 'Vendredi';
                break;
              case 'saturday':
                $dayfr = 'Samedi';
                break;
              case 'sunday':
                $dayfr = 'Dimanche';
                break;
              default:
                # code...
                break;
            }

            $data_wish .= " - ".ucfirst($wish['activity']['label'])." du ".$dayfr." à (".$wish['time_start']. " - ".$wish['time_end']. ") <br />";
          }
        }

        // Send Email Notification
        $mailjet = Mailjet::getClient();

        $body = [
          'FromEmail' => getenv('MAIL_FROM_ADDRESS'),
          'FromName' => getenv('MAIL_FROM_NAME'),
          'Subject' =>  "Activation de compte",
          'Text-part' => "PLE Plaisirs de l'eau",
          'Html-part' => "<p>Bonjour ".ucfirst($customer->first_name).",</p>
                          <br />Félicitation ! Vous êtes maintenant client(e) de PLE. Vous avez souscrit aux activités suivantes :<br /> "
                          .$data_wish. " <br /> 
                          L'EQUIPE DE LES PLAISIRE DE L'EAU  vous remercie ! À très bientôt ! " ,

          'Recipients' => [['Email' => $customer->email]]
        ];

        $mailjet->post(Resources::$Email, ['body' => $body]);
        // End Send Email NOtification

      }
    } else {
      $customer->update([
        'status' => 'old_customer',
        'status_changed_at' => now(),
      ]);
    }

    // if($request->activated){
    // Mail::to($customer)->send(new ProfilActivated($customer));
    // }

    $message = 'Compte ' . (($request->activated) ? 'activé' : 'désactivé');
    return response()->json(['success', $message]);
  }
}
