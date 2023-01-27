<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Webi\Events\WebiUserLogged;

class WebiLogged extends Controller
{
	function index(Request $request)
	{
		$this->setWebiCookie($request);

		if (Auth::check()) {
			WebiUserLogged::dispatch(Auth::user(), $request->ip());

			return response()->success([
				'message' => trans('Authenticated.'),
				'locale' => app()->getLocale(),
				'user' => Auth::user()
			]);
		} else {
			return response()->errors([
				'message' => trans('Not authenticated.'),
				'locale' => app()->getLocale(),
				'user' => null
			]);
		}
	}

	function setWebiCookie($request)
	{
		if (!empty($request->cookie('webi_token'))) {
			$token = Str::uuid();
			// Set cookie: $name, $val, $minutes, $path, $domain, $secure, $httpOnly = true, $raw = false, $sameSite = 'strict'
			Cookie::queue(
				'webi_token',
				$token,
				env('APP_REMEBER_ME_MINUTES', 3456789),
				'/',
				'.' . request()->getHost(),
				request()->secure(), // or true for https only
				true,
				false,
				'lax' // Or strict for max security
			);
		}
	}
}
