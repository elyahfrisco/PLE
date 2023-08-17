<?php

namespace App\Http\Controllers;

use App\Models\mailTemplate;
use Illuminate\Http\Request;

class MailTemplateController extends Controller
{
  public function _list()
  {
    return mailTemplate::paginate();
  }

  public function destroy($id)
  {
    $data = mailTemplate::find($id);
    $title = $data->title;
    $data->delete();
    return back()->with('info', "Le modèle : $title - a été supprimé");
  }
}
