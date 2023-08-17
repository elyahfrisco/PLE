<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:sanctum', 'verified']);
  }

  public function index(Request $request)
  {
    //  dd(auth()->user()->role());
    //  dd(auth()->user()->roles);

    Inertia::share('page_title', "Tableau de bord   ");

    if (auth()->user()->isAdmin() || auth()->user()->isAssistant()) {
      return $this->admin($request);
    } elseif (auth()->user()->isProspect()) {
      return $this->prospect();
    } elseif (auth()->user()->isCustomer()) {
      return $this->customer();
    } elseif (auth()->user()->isCoach()) {
      return $this->coach();
    } elseif (auth()->user()->isAssistant()) {
      return $this->assistant();
    } elseif (auth()->user()->isIntervenant()) {
      return $this->intervenant();
    }
  }

  private function admin(Request $request)
  {
    $users = null;
    // dd($sers);
    if ($request->q || $request->filterBy) {
      $query = User::with('phones', 'activity', 'wishes', 'subscription_activities')
        ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
        ->select('users.*')
        ->where('role_user.role_id', '2');
      if (!empty($request->q)) {
        /*$key = $request->q;
                    $query->where("users.id",$key)
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
                    $query->whereHas('phones', function (Builder $q) use($key) {
                        $q->whereRaw("phone LIKE '%".$key."%' ");
                        $q->orWhereRaw("search_key LIKE '%".$key."%' ");
                    });*/
        $query->search();
      }
      if (request()->has('filterBy')) {
        if (!empty($request->get('filterBy')['subscription_status'])) {
          $value = $request->get('filterBy')['subscription_status'];
          if ($value === "customer") {
            $query->where('activated', 1)
              ->where(function ($q) {
                $q->where('status', '!=', 'old_customer')
                  ->orWhere('status', null);
              });
          }
          if ($value === "prospect") {
            $query->where('activated', 0);
          }
          if ($value === "old_customer") {
            $query->where("status", "old_customer");
          }
          if ($value === "waiting_customer") {
            $query->where("activated", 2);
          }
        }
        // if(!empty($request->get('filterBy')['planning_id'])){
        //     $query->leftJoin('subscription_activities','users.id','=','subscription_activities.user_id')
        //     ->where('subscription_activities.planning_id', $request->get('filterBy')['planning_id']);
        // }
        if (!empty($request->get('filterBy')['created_date'])) {
          $query->whereDate("created_at", $request->get('filterBy')['created_date']);
        }
        // if(!empty($request->get('filterBy')['activity_id'])){
        //     $query->leftJoin('subscription_activities','users.id','=','subscription_activities.user_id')
        //     ->where('subscription_activities.activity_id', $request->get('filterBy')['activity_id']);
        // }
        $query->filter();
      }

      $users = $query->paginate(10)
        ->appends(request()->query());
      //dd($users);
    }
    return Inertia::render('Admin/Dashboard/index', compact('users'));
  }

  private function customer()
  {
    return Inertia::render('Customer/Dashboard/index');
  }

  private function prospect()
  {
    return Inertia::render('Prospect/Dashboard/index');
  }

  private function coach()
  {
    return Inertia::render('Coach/Dashboard/index');
  }

  private function assistant()
  {
    return Inertia::render('Assistant/Dashboard/index');
  }

  private function intervenant()
  {
    return Inertia::render('Intervenant/Dashboard/index');
  }
}
