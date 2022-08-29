<?php

namespace Webi\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Webi\Events\WebiUserLogged;
use Webi\Http\Requests\WebiLoginRequest;

class WebiLogin extends Controller
{
	function index(WebiLoginRequest $request)
	{
		$valid = $request->validated();

		$remember = !empty($valid['remember_me']) ? true : false;

		unset($valid['remember_me']);

		if (Auth::attempt($valid, $remember)) {

			$user = Auth::user(); // request()->user();

			if (!$user instanceof User) {
				throw new Exception('Invalid credentials.', 422);
			}

			if (empty($user->email_verified_at)) {
				throw new Exception('The account has not been activated.', 422);
			}

			if (
				$remember == true
				&& !empty($user->remember_token)
			) {
				// $name, $val, $minutes, $path,
				// $domain, $secure, $httpOnly = true,
				// $raw = false, $sameSite = 'strict'
				Cookie::queue(
					'_remember_token',
					$user->remember_token,
					env('APP_REMEBER_ME_MINUTES', 3456789),
					'/',
					'.' . request()->getHost(),
					request()->secure(),
					true,
					false,
					'strict'
				);
			}

			request()->session()->regenerate();

			// Event
			WebiUserLogged::dispatch($user, request()->ip());

			return response()->json([
				'message' => trans('Authenticated.'),
				'user' => $user,
			], 200);
		} else {
			throw new Exception('Invalid credentials.', 422);
		}
	}
}
