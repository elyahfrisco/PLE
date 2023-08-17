<?php

namespace App\Http\Middleware;

use Closure;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Establishment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class SetSessionData
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    if (Auth::user()) {

      if (session()->get('auth.guest_mode') && !$request->cookie('guest_mode')) {
        try {
          Auth::logout();
          $request->session()->invalidate();
          $request->session()->regenerateToken();
        } catch (\Throwable $th) {
        }
        return redirect('/login')->with('error', 'session expiré');
      }

      if (!session()->get('auth.role_name') || Auth::user()->id != (session()->get('auth')->id ?? false)) {
        $auth = Auth::user();
        $auth->role_id = $auth->roles()->first()->id;
        $auth->role_name = $this->role($auth);

        if ($request->cookie('guest_mode') !== null) {
          $auth->guest_mode = json_decode($request->cookie('guest_mode'))->id;
        }

        session()->put('auth', $auth);
      }

      Inertia::share('auth_user', function () {
        return session()->get('auth');
      });

      if (!session()->get('establishments_list')) {
        session()->put('establishments_list', Establishment::all());
      }

      Inertia::share('max_session_for_renewal', 4);

      Inertia::share('permissions', [
        'edit_subscription' => auth()->user()->isAdmin(),
        'comment_subscription' => auth()->user()->isAdmin(),
        'create_subscription_fro_renewal' => auth()->user()->isAdmin(),
        'edit_recuperation_request' => auth()->user()->isAdmin(),
      ]);
    }

    $route_name = Route::currentRouteName();
    Inertia::share('current_route_name', $route_name);
    Inertia::share('current_url', url()->current());

    Inertia::share('req', request()->all());


    if ($route_name == 'login') {
      Inertia::share('page_title', 'Se connecter');
    } else if ($route_name == 'signup') {
      Inertia::share('page_title', "S'inscrire");
    }

    return $next($request);
  }

  private function role($user)
  {
    if ($user->isAdmin()) {
      return "admin";
    } elseif ($user->isProspect()) {
      return "prospect";
    } elseif ($user->isCustomer()) {
      return "customer";
    } elseif ($user->isCoach()) {
      return "coach";
    } elseif ($user->isAssistant()) {
      return "assistant";
    } elseif ($user->isIntervenant()) {
      return "intervenant";
    }
  }
}
