<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Prospect\RegistrationController;

Route::get("signup", [RegistrationController::class, 'singup'])->name('signup');
Route::post("signup", [RegistrationController::class, 'store'])->name('prospects.store');
