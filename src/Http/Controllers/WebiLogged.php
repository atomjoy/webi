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
		if (!Auth::check()) {
			// Remember me auth
			$token = $request->cookie('_remeber_token');

			if (!empty($token)) {

				$user = User::where([
					'remember_token' => $token
				])->whereNotNull('email_verified_at')
					->whereNull('deleted_at')
					->first();

				if ($user instanceof User) {
					$request->session()->regenerate();
					Auth::login($user, true);
				}
			}
		}

		if (Auth::check()) {
			// Event
			WebiUserLogged::dispatch(Auth::user(), request()->ip());

			return $this->jsonResponse('Authenticated via remember me.', [
				'locale' => app()->getLocale(),
				'user' => Auth::user()
			]);
		} else {
			return $this->jsonResponse('Not authenticated.', [
				'locale' => app()->getLocale(),
				'user' => null
			], 422);
		}
	}
}
