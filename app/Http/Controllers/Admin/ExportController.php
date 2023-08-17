<?php

namespace App\Http\Controllers\Admin;

use App\Console\ScheduleJobs;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ExportController extends Controller
{
  public function index()
  {
    ScheduleJobs::cleanOldExportFile();
    
    $exported_files = collect(Storage::allFiles('public/export/customer'))
      ->filter(function ($file) {
        return strpos('~$', $file) === false;
      })
      ->map(function ($file) {
        return Cache::remember($file, 60 * 60 * 24 * 3, function () use ($file) {
          $name = str_replace('public/export/customer/', '', $file);
          return [
            'name' => $name,
            'url' => url(Storage::url($file)),
            'size' => formatBytes(Storage::size($file)),
            'lastModified' => Carbon::parse(Storage::lastModified($file)),
            'delete_at' => Carbon::parse(Storage::lastModified($file))->addDays(5)->diffForHumans(),
          ];
        });
      })->sortByDesc(function ($item) {
        return $item['lastModified']->timestamp;
      })->toArray();

    return Inertia::render('Admin/Export/index', compact('exported_files'));
  }
}
