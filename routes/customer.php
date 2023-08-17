<?php

use App\Http\Controllers\ApiController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\Customer\PlanningController;

Route::middleware(['auth:sanctum'])->group(function () {
  Route::get("establishments/{establishment_id}/planning", [PlanningController::class, 'index'])->name('plannings.establishment');
  Route::get("plannings", [PlanningController::class, 'myPlaning'])->name('plannings.customer');
});
