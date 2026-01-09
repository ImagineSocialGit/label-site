<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\SessionsController;

Route::get('/', [SiteController::class, 'index']);

Route::get('/admin-' . config('app.initials') . '/login', [SessionsController::class, 'create']);
Route::post('/admin-' . config('app.initials') . '/login', [SessionsController::class, 'store'])->middleware('guest');
Route::post('/admin-' . config('app.initials') . '/logout', [SessionsController::class, 'destroy'])->middleware('auth');

Route::get('/{artist:slug}', [ArtistController::class, 'show']);
Route::get('/admin-' . config('app.initials') . '/artists/add', [ArtistController::class, 'create'])->middleware('auth');
Route::post('/admin-' . config('app.initials') . '/artists/add', [ArtistController::class, 'store'])->middleware('auth');
Route::get('/admin-' . config('app.initials') . '/artists/edit/{artist}', [ArtistController::class, 'edit'])->middleware('auth');
Route::patch('/admin-' . config('app.initials') . '/artists/edit/{artist}', [ArtistController::class, 'update'])->middleware('auth');