<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\Coach\PlanningController;

Route::middleware([])->group(function () {
  Route::get("establishments/{establishment_id}/planning", [PlanningController::class, 'index'])->name('plannings.establishment');
});
