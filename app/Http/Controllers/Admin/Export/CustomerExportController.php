<?php

namespace App\Http\Controllers\Admin\Export;

use App\Http\Controllers\Controller;
use App\Jobs\CustomerExportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

class CustomerExportController extends Controller
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Request $request)
  {
    $filters = $request->toArray();
    $user_ip_address = request()->ip;
    $auth_user_id = auth()->user()->id;

    CustomerExportJob::dispatchAfterResponse(
      $filters,
      $user_ip_address,
      $auth_user_id
    );

    return back()->with('success', "Le fichier est en cours de génération. Vous pouvez visiter le menu \"Fichier exporté\" pour voir le fichier une fois l'export terminé.");
  }
}
