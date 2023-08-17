<?php

namespace App\Http\Controllers;

use App\Mail\NewProspect;
use DateTime;
use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Activity;
use App\Models\UserWish;
use App\Models\UserPhone;
use App\Models\UserComment;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ProfilActivated;
use App\Models\ContactOrigin;
use App\Models\Establishment;
use App\Models\ActivityPassGroup;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\CustomerRequest;
use App\Models\AbsencePrevention;
use App\Models\Bill;
use App\Models\Queue;
use App\Models\RecuperationRequest;
use App\Models\Renewal;
use App\Models\UserFee;

use \Mailjet\Resources;
use Mailjet\LaravelMailjet\Facades\Mailjet;

class AccountController extends Controller
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
   * Display information compte
   *
   * @return \Illuminate\Http\Response
   */

  public function index($id = null)
  {
    $user = $id ? User::findOrFail($id) : Auth::user();

    if (!$user) {
      return redirect('/login');
    }

    $user = User::withTrashed()->with(['phones', 'activity', 'wishes', 'first_session'])->with('comments', function ($query) {
      $query->with('commentator', function ($query) {
        $query->select('id', 'name', 'first_name', 'profile_photo_path');
      })
        ->orderByDesc('id');
    })->find($user->id);

    $user->role = $this->role($user);

    return Inertia::render('Account/index', compact('user'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   */
  public function edit($id = null)
  {
    $user = $id ? User::find($id) : Auth::user();

    if (!$user) {
      return redirect('/login');
    }

    extract($this->form_data());

    $user = User::withTrashed()->with(['phones', 'activity', 'wishes'])->find($user->id);
    $user->role = $this->role($user);
    $prospectForm = (!$user->activated) && ($this->role($user) == 'customer');
    return Inertia::render('Account/edit', compact('user', 'contact_origins', 'establishments', 'activities', 'prospectForm'));
  }

  private function form_data()
  {
    return [
      'days' => $this->days,
      'activities' => Activity::orderBy('name')->get(),
      'contact_origins' => ContactOrigin::orderBy('designation')->get(),
      'establishments' => Establishment::orderBy('name')->where('relaxation_center', false)->get(),
      'account_type' => request()->get('account_type'),
    ];
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(CustomerRequest $request)
  {
    
    if (auth()->user()->role_name = "admin") {
      $password = 'password';
    }

    $request->offsetSet('password', Hash::make($password));

  
    $data = $request->all();

    if (request()->is_child) {
      $data['email'] = "e" . (intval(User::max('id')) + 1) . "@ple.fr";
    }

    $user = User::create($data);


    if ($request->phone)
      $user->phones()->create(['phone' => $request->phone, 'type' => $request->type]);

    if ($request->phone2) {
      $user->phones()->create(['phone' => $request->phone2, 'type' => $request->type2]);
    }

    if ($request->account_type == 'coach') {
      $user->roles()->attach(3);
      $user->activated = 1;
    } elseif ($request->account_type == 'admin') {
      $user->roles()->attach(1);
      $user->activated = 1;
    } elseif ($request->account_type == 'intervenant') {
      $user->roles()->attach(5);
      $user->activated = 1;
    } elseif ($request->account_type == 'assistant') {
      $user->roles()->attach(4);
      $user->activated = 1;
    } else {
      $user->roles()->attach(2);
      if ($request->account_type == 'customer') {
        $user->activated = 1;
        // NewCustomerNotification

      } else {
        dispatch(function () use ($data, $request) {
          Mail::to($request->email)->send(new NewProspect($data['wishes']));
        })->afterResponse();

        Cache::forget('prospects_roles_count');
      }
    }

    $user->email_verified_at = now();

    $user->save();

    $data_wish = '';

    if (request()->wishes) {
      foreach (request()->wishes as $key => $wish) {
        $user->wishes()->create($wish);
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

        $data_wish .= " - ".ucfirst($wish['activity']['name'])." du ".$dayfr." à (".$wish['time_start']. " - ".$wish['time_end']. ") <br />";
      }
    }

    if ($request->account_type == 'prospect') {

      // Send Email Notification
      $mailjet = Mailjet::getClient();

      $body = [
        'FromEmail' => getenv('MAIL_FROM_ADDRESS'),
        'FromName' => getenv('MAIL_FROM_NAME'),
        'Subject' =>  "Liste de ses activités souhaitées",
        'Text-part' => "PLE Plaisirs de l'eau",
        'Html-part' => "<p>Bonjour ".ucfirst($request->first_name).",</p>
                        <br />Ci-dessous une récapitulation des activités sur lesquelles vous souhaitez souscrire :<br /> "
                        .$data_wish. " <br /> 
                        L'EQUIPE DE LES PLAISIRE DE L'EAU  vous remercie ! À très bientôt ! " ,

        'Recipients' => [['Email' => $request->email]]
      ];

      $mailjet->post(Resources::$Email, ['body' => $body]);
      // End Send Email NOtification

      session()->flash('success', "Prospect ajouté avec success");
    } elseif ($request->account_type == 'coach') {
      session()->flash('success', "Enseignant ajouté avec success");
    } else {
      session()->flash('success', "Client ajouté avec success");
    }

    if ($request->create_for_subscription) {
      return redirect()->route('subscriptions.create', [
        "user_id" => $user->id,
      ]);
    } else {
      return back();
    }
  }


  /**
   * Update the user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(CustomerRequest $request, $id = null)
  {

    if ($request->filled('password')) {
      $request->offsetSet('password', Hash::make($request->password));
    }

    $user = $id ? User::find($id) : Auth::user();
    $user->update($request->all());

    /*Delete phones*/
    $id_updated = Arr::pluck($request->phones, 'id');
    $user->phones()->whereNotIn('id', $id_updated)->delete();

    $data_phones = array_map(function ($phone) {
      return Arr::only($phone, ['id', 'phone', 'owner', 'type', 'user_id']);
    }, $request->phones);

    /* Update or insert phones */
    UserPhone::upsert(
      $data_phones,
      ['id'],
      ['phone', 'owner', 'type', 'user_id'],
    );

    if ($request->wishes) {
      foreach ($request->wishes as $key => $wish) {
        if ($wish['deleted'] ?? false) {
          if ($wish['id'] ?? false) {
            UserWish::destroy($wish['id']);
          }
        } elseif ($wish['id'] ?? false) {
          UserWish::find($wish['id'])->update($wish);
        } else {
          $user->wishes()->create($wish);
        }
      }
    }

    $user->activities()->detach();
    $user->activities()->attach($request->activity_id);

    forget_cache('user_activities_' . $user->id . '_wished');

    session()->flash('success', "Profil modifié");

    return back();
  }

  /**
   * Show the form for editing the customer's profil photo.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit_photo($id = null)
  {
    $user = $id ? User::find($id) : Auth::user();
    return Inertia::render('Account/editPhoto', compact('user'));
  }

  /**
   * Update the customer's profil photo.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update_photo(Request $request, $id = null)
  {
    $request->validate([
      'profile_photo' => "required|max:2048"
    ]);

    $user = $id ? User::find($id) : Auth::user();

    $fileName =  $user->id . "_" . time() . '.' . $request->profile_photo->extension();

    $request->profile_photo->move(public_path(config('app.path_profil_photo')), $fileName);

    $user->profile_photo_path = $fileName;

    $user->save();

    session()->flash('success', "Profil modifié");

    return back();
  }

  /**
   * Show the form for editing the customer's medical certifiacte.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit_medical_certificate($id = null)
  {
    $user = $id ? User::find($id) : Auth::user();
    return Inertia::render('Account/editMedicalCertificate', compact('user'));
  }

  /**
   * Update the customer's medical certifiacte.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update_medical_certificate(Request $request, $id = null)
  {
    $request->validate([
      'medical_certificate' => "required|max:2048|mimes:pdf,doc,docx,png,jpg"
    ]);

    $user = $id ? User::find($id) : Auth::user();

    $fileName =  $user->id . "_" . $user->name . "_medical_certificate_" . time() . '.' . $request->medical_certificate->extension();

    $path = $request->medical_certificate->move(public_path(config('app.path_medical_certificate')), $fileName);

    $user->medical_certificate_path = $fileName;

    $user->save();

    session()->flash('success', "Cértificat médical modifié");

    return back();
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id = null)
  {
    $user = $id ? User::find($id) : Auth::user();
    $role = $this->role($user);

    Queue::where('user_id', $user->id)->delete();
    AbsencePrevention::where('user_id', $user->id)->delete();
    Bill::where('user_id', $user->id)->delete();
    RecuperationRequest::where('user_id', $user->id)->delete();
    UserFee::where('user_id', $user->id)->delete();

    $user->subscriptions()->get()->each(function ($subscription) {
      $subscription->renewal()->delete();
      $subscription->activities()->delete();
      $subscription->comments()->delete();
      $subscription->delete();
    });

    $user->forceDelete();

    forget_cache('user_activities_' . $user->id . '_wished');
    forget_cache('prospects_roles_count');

    if (auth()->user()->isAdmin()) {
      if ($role == 'coach') {
        return redirect()->route('coach.index')->with('success', 'Coach supprimé');
      } elseif ($role == "customer") {
        return redirect()->route('customers.index')->with('success', 'Client supprimé');
      } else {
        return redirect()->route('customers.index', ['account_type' => 'prospect'])->with('success', 'Client supprimé');
      }
    } else {
      return back()->with('success', 'Compte supprimé');
    }
  }

  private function role($user)
  {
    if ($user->isAdmin()) {
      return "admin";
    } elseif ($user->isAssistant()) {
      return "assistant";
    } elseif ($user->isCustomer()) {
      return "customer";
    } elseif ($user->isCoach()) {
      return "coach";
    } elseif ($user->isProspect()) {
      return "prospect";
    } elseif ($user->isAttente()) {
      return "attente";
    } elseif ($user->isIntervenant()) {
      return "intervenant";
    }
  }
}
