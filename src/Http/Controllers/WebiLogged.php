<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webi\Events\WebiUserLogged;
use Webi\Traits\Http\HasJsonResponse;

class WebiLogged extends Controller
{
	use HasJsonResponse;

	function index(Request $request)
	{
		if (Auth::viaRemember()) {
			WebiUserLogged::dispatch(Auth::user(), request()->ip());

			return $this->jsonResponse('Authenticated via remember me.', [
				'locale' => app()->getLocale(),
				'user' => Auth::user()
			]);
		}

		if (Auth::check()) {
			WebiUserLogged::dispatch(Auth::user(), request()->ip());

			return $this->jsonResponse('Authenticated.', [
				'locale' => app()->getLocale(),
				'user' => Auth::user()
			]);
		}

		return $this->jsonResponse('Not authenticated.', [
			'locale' => app()->getLocale(),
			'user' => null
		]);
	}
}
