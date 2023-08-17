<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware([])->group(function () {
    Route::get("{id}", [PostController::class, 'view'])->name('view');
});
