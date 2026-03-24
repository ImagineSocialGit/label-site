<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\PageStyleController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'index'])->middleware('staging.auth');

Route::get('/error', function(){
    return view('error');
})->middleware('staging.auth')->name('error');

Route::get('/staging-login', function () {
    return view('sessions.staging-create');
})->name('staging.login');

Route::get('/admin-' . config('app.initials') . '/login', [SessionsController::class, 'create']);
Route::post('/admin-' . config('app.initials') . '/login', [SessionsController::class, 'store'])->middleware('guest');
Route::post('/admin-' . config('app.initials') . '/logout', [SessionsController::class, 'destroy'])->middleware('auth');

Route::get('/{artist:slug}', [ArtistController::class, 'show'])->middleware('staging.auth');
Route::get('/admin-' . config('app.initials') . '/artists', [ArtistController::class, 'index'])->middleware('auth');
Route::get('/admin-' . config('app.initials') . '/artists/add', [ArtistController::class, 'create'])->middleware('auth');
Route::post('/admin-' . config('app.initials') . '/artists/add', [ArtistController::class, 'store'])->middleware('auth');
Route::get('/admin-' . config('app.initials') . '/artists/edit/{artist:slug}', [ArtistController::class, 'edit'])->middleware('auth');
Route::patch('/admin-' . config('app.initials') . '/artists/edit/{artist}', [ArtistController::class, 'update'])->middleware('auth');
Route::patch('/admin-' . config('app.initials') . '/artists/{artist:slug}/pageStyles/push', [PageStyleController::class, 'push'])->middleware('auth');

Route::get('/admin-' . config('app.initials') . '/artists/{artist:slug}/music', [MusicController::class, 'index'])->middleware('auth');
Route::get('/admin-' . config('app.initials') . '/artists/{artist:slug}/music/add', [MusicController::class, 'create'])->middleware('auth');
Route::post('/admin-' . config('app.initials') . '/artists/{artist:slug}/music/add', [MusicController::class, 'store'])->middleware('auth');
Route::delete('/admin-' . config('app.initials') . '/artists/{artist:slug}/music/{music}/delete', [MusicController::class, 'destroy'])->middleware('auth');

Route::get('/{artist:slug}/{post:slug}', [PostController::class, 'show'])->middleware('staging.auth');
Route::get('/admin-' . config('app.initials') . '/artists/{artist:slug}/news', [PostController::class, 'index'])->middleware('auth');
Route::get('/admin-' . config('app.initials') . '/artists/{artist:slug}/news/add', [PostController::class, 'create'])->middleware('auth');
Route::post('/admin-' . config('app.initials') . '/artists/{artist:slug}/news/add', [PostController::class, 'store'])->middleware('auth');
Route::post('/admin-' . config('app.initials') . '/artists/{artist:slug}/news/push', [PostController::class, 'push'])->middleware('auth');