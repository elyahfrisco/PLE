<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

include('cache_helpers.php');

if (!function_exists('is_local')) {
  function is_local()
  {
    return request()->server("SERVER_NAME") == '127.0.0.1';
  }
}

if (!function_exists('daysName')) {
  function daysName()
  {
    return ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
  }
}

if (!function_exists('daysNameArrayKey')) {
  function daysNameArrayKey()
  {
    return ['monday' => [], 'tuesday' => [], 'wednesday' => [], 'thursday' => [], 'friday' => [], 'saturday' => [], 'sunday' => []];
  }
}

if (!function_exists('dToFr')) {
  function dToFr($day_name)
  {
    $daysfr = [
      'monday' => 'lundi',
      'tuesday' => 'mardi',
      'wednesday' => 'mercredi',
      'thursday' => 'jeudi',
      'friday' => 'vendredi',
      'saturday' => 'samedi',
      'sunday' => 'dimanche'
    ];
    return $daysfr[strtolower($day_name)] ?? null;
  }
}

if (!function_exists('dEngFrSigle')) {
  function dEngFrSigle($sigle)
  {
    switch ($sigle) {
      case 'LU':
        return 'monday';
        break;
      case 'MA':
        return 'tuesday';
        break;
      case 'ME':
        return 'wednesday';
        break;
      case 'JE':
        return 'thursday';
        break;
      case 'VE':
        return 'friday';
        break;
      case 'SA':
        return 'saturday';
        break;
      case 'DI':
        return 'sunday';
        break;
    }
  }
}


if (!function_exists('n_d2')) {
  function n_d2($price)
  {
    return number_format($price, 2, ',', ' ');
  }
}

if (!function_exists('ht_price')) {
  function ht_price($price, $tva = 20)
  {
    return n_d2(($price * (100 - $tva)) / 100);
  }
}

if (!function_exists('tva_price')) {
  function tva_price($price, $tva = 20)
  {
    return n_d2(($price * $tva) / 100);
  }
}

if (!function_exists('is_date')) {
  function is_date($date)
  {
    $_ = explode('-', $date);
    return count($_) >= 3;
  }
}

if (!function_exists('clear_accent')) {
  function clear_accent($string_)
  {
    $search = explode(",", "ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
    $replace = explode(",", "c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
    return  str_replace($search, $replace,  Str::kebab($string_));
  }
}

if (!function_exists('dev_mail')) {
  function dev_mail()
  {
    return env('DEV_MAIL', 'dev06@kawa-group.fr');
  }
}

if (!function_exists('arret')) {
  function arret($v = 'arret')
  {
    dd($v);
  }
}

if (!function_exists('dq')) {
  function dq($v = 'arret')
  {
    if (request()->dd) {
      dd($v);
    }
  }
}

if (!function_exists('dmq')) {
  function dmq($v = 'arret')
  {
    if (request()->dd) {
      dump($v);
    }
  }
}

if (!function_exists('empty_')) {
  function empty_($value)
  {
    $value = trim($value);

    if (empty($value))
      return true;
    elseif ($value === 0)
      return true;
    elseif (strlen($value) == 0)
      return true;
    elseif (strlen($value) > 0)
      return false;
    elseif ($value === false)
      return true;
    elseif ((($value[0] ?? false) || ($value[0] ?? false) == 0  ? false : true))
      return true;
    else
      return false;
  }
}

if (!function_exists('genere_user_mail')) {
  function genere_user_mail()
  {
    return "c" . Str::random(4) . "@lesplaisirsdeleau.fr";
  }
}

if (!function_exists('default_mail')) {
  function default_mail($user_id)
  {
    return "c" . $user_id . Str::random(1) . "@lesplaisirsdeleau.fr";
  }
}

if (!function_exists('count_job')) {
  function count_job(...$job_names)
  {
    return DB::table('jobs')
      ->whereIn('queue', $job_names)
      ->count();
  }
}


if (!function_exists('renderJsonOrInertia')) {
  function renderJsonOrInertia($component, $data = [])
  {
    if (Request::hasHeader('X-Inertia') == false && Request::expectsJson()) {
      return response()->json($data);
    }
    return Inertia::render($component, $data);
  }
}

if (!function_exists('wantInertia')) {
  function wantInertia()
  {
    return Request::hasHeader('X-Inertia') == true;
  }
}

if (!function_exists('page_limit')) {
  function page_limit()
  {
    return 50;
  }
}

if (!function_exists('formatBytes')) {
  function formatBytes($size, $precision = 2)
  {
    $base = log($size, 1024);
    $suffixes = array('o', 'Ko', 'Mo', 'Go', 'To');

    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
  }
}

if (!function_exists('return_real_mail')) {
  function return_real_mail($email)
  {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      return null;

    return preg_match("/^..{1,6}@(lesplaisirsdeleau\.fr|ple.fr)$/", $email) ? null : $email;
  }
}
