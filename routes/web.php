<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;

Route::get('/', [SiteController::class, 'index']);

Route::get('/welcome-2', [SiteController::class, 'index2']);

Route::get('/welcome-3', [SiteController::class, 'index3']);
