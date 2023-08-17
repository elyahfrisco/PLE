<?php

namespace App\Providers;

use Inertia\Inertia;
use App\Models\Establishment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use NascentAfrica\Jetstrap\JetstrapFacade;

class AppServiceProvider extends ServiceProvider
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

  protected $daysfr = [
    'monday' => 'lundi',
    'tuesday' => 'mardi',
    'wednesday' => 'mercredi',
    'thursday' => 'jeudi',
    'friday' => 'vendredi',
    'saturday' => 'samedi',
    'sunday' => 'dimanche'
  ];

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {

    Inertia::share('user', function () {
      return [
          'roles' => optional(Auth::user())->roles,
          // Ajoutez d'autres données de l'utilisateur que vous souhaitez partager ici
      ];
     });

     
    Inertia::share('flash', function () {
      return [
        'success' => session()->get('success'),
        'info' => session()->get('info'),
        'warning' => session()->get('warning'),
        'error' => session()->get('error'),
      ];
    });

    Inertia::share('appName', config('app.name'));
    Inertia::share('appPathProfilPhoto', config('app.path_profil_photo'));
    Inertia::share('appPathMedicalCertificate', config('app.path_medical_certificate'));

    try {
      Inertia::share('establishments_list', Cache::remember('establishments_list', (60 * 60), function () {
        return Establishment::all();
      }));
    } catch (\Throwable $th) {
    }

    Inertia::share('days', $this->days);
    Inertia::share('daysfr', $this->daysfr);

    Inertia::share('logged', function () {
      return Auth::user() ? true : false;
    });

    Paginator::useBootstrap();
    // JetstrapFacade::useAdminLte3();
    Schema::defaultStringLength(191);

    Collection::macro('recursive', function () {
      return $this->map(function ($value) {
        if (is_array($value) || is_object($value)) {
          return collect($value)->recursive();
        }
        return $value;
      });
    });
  }
}
