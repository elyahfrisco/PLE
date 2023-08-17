<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\ImportCustomerJob;
use App\Jobs\ImportCustomerJob2;

class ImportCustomer extends Controller
{
  public function index()
  {
    $max_file_upload = (ini_get('max_file_uploads'));
    return Inertia::render('Account/Import/index', compact('max_file_upload'));
  }

  public function store_import_excel(Request $request)
  {

    $request->validate([
      'customers_imported' => 'required',
    ]);

    if ($request->type == 2) {
      $request->validate([
        'establishment_id' => 'required',
        'season_id' => 'required',
        'activity_id' => 'required',
      ]);
    }

    foreach ($request->customers_imported as $key => $file_imported) {
      $extension_ = (explode('.', $file_imported->getClientOriginalName()));
      $extension = $extension_[count($extension_) - 1];
      $fileName = Str::uuid() . '-' . time() . '.' . $extension;
      $path = $file_imported->move(storage_path('app/public/' . config('app.path_import_customer')), $fileName);

      if ($request->type == 2) {
        $subscription_info = $request->except('customers_imported', 'type', 'date');

        // ImportCustomerJob2::dispatchSync($fileName, $extension, $subscription_info);
        ImportCustomerJob2::dispatch($fileName, $extension, $subscription_info)->onQueue('customer_import');
      } else {
        $type_user = $request->get('type_user', 'customer');
        $establishment_id = $request->get('establishment_id', 1);

        if (is_numeric(str_replace('.', '', $extension))) {
          $extension = 'csv';
        }

        // ImportCustomerJob::dispatchSync($fileName, $extension, $type_user, $establishment_id);
        ImportCustomerJob::dispatch($fileName, $extension, $type_user, $establishment_id)->onQueue('customer_import');
      }
    }

    return redirect()->route('customer.import')->with('success', "Importation des clients en cours...");
  }
}
