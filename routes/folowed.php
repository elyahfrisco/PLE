<?php

use App\Http\Controllers\FollowedUserController;
use Illuminate\Support\Facades\Route;

Route::middleware([])->group(function () {
  Route::get("user", [FollowedUserController::class, 'index'])->name('user.index');
});
