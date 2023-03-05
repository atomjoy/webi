<?php

use Illuminate\Support\Facades\Route;
use Webi\Http\Controllers\WebiActivate;
use Webi\Http\Controllers\WebiCsrf;
use Webi\Http\Controllers\WebiLocale;
use Webi\Http\Controllers\WebiLogged;
use Webi\Http\Controllers\WebiLogin;
use Webi\Http\Controllers\WebiLogout;
use Webi\Http\Controllers\WebiRegister;
use Webi\Http\Controllers\WebiPassReset;
use Webi\Http\Controllers\WebiPassChange;
use Webi\Http\Controllers\WebiPassConfirm;
use Webi\Http\Controllers\WebiUserDetails;

Route::prefix('web/api')->name('web.api.')->middleware([
	'web', 'webi-locale', 'webi-json'
])->group(function () {

	// Public routes
	Route::post('/login', [WebiLogin::class, 'index'])->name('login');
	Route::post('/register', [WebiRegister::class, 'index'])->name('register');
	Route::post('/reset', [WebiPassReset::class, 'index'])->name('reset');
	Route::get('/activate/{id}/{code}', [WebiActivate::class, 'index'])->name('activate');
	Route::get('/logged', [WebiLogged::class, 'index'])->name('logged');
	Route::get('/csrf', [WebiCsrf::class, 'index'])->name('csrf');
	Route::get('/locale/{locale}', [WebiLocale::class, 'index'])->name('locale');

	// Only logged users
	Route::middleware(['auth', 'webi-role:admin|worker|user'])->group(function () {
		Route::post('/change-password', [WebiPassChange::class, 'index'])->name('change-password');
		Route::post('/confirm-password', [WebiPassConfirm::class, 'index'])->name('confirm-password');
		Route::get('/logout', [WebiLogout::class, 'index'])->name('logout');
		Route::get('/test/user', [WebiUserDetails::class, 'index'])->name('test.user');
	});

	// Only logged admin
	Route::middleware(['auth', 'webi-role:admin|worker'])->group(function () {
		Route::get('/test/worker', [WebiUserDetails::class, 'index'])->name('test.worker');
	});

	// Only logged admin
	Route::middleware(['auth', 'webi-role:admin'])->group(function () {
		Route::get('/test/admin', [WebiUserDetails::class, 'index'])->name('test.admin');
	});

	// Fallback
	Route::fallback(function () {
		return response()->errors('Invalid api route path or request method.', 400);
	});
});
