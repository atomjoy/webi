<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Webi\Events\WebiUserLogged;
use Webi\Exceptions\WebiException;
use Webi\Http\Requests\WebiLoginRequest;
use Webi\Traits\Http\HasJsonResponse;

class WebiLogin extends Controller
{
	use HasJsonResponse;

	function index(WebiLoginRequest $request)
	{
		$valid = $request->validated();

		$remember = !empty($valid['remember_me']) ? true : false;

		unset($valid['remember_me']);

		if (Auth::attempt($valid, $remember)) {

			$user = Auth::user(); // request()->user();

			if (!$user instanceof User) {
				throw new WebiException('Invalid credentials.');
			}

			if (empty($user->email_verified_at)) {
				throw new WebiException('The account has not been activated.');
			}

			if ($remember == true && request()->secure()) {
				// Create token
				if (empty($user->remember_token)) {
					$user->remember_token = uniqid() . md5(microtime());
					$user->save();
				}

				// Set cookie
				if (!empty($user->remember_token)) {
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
			}

			request()->session()->regenerate();

			// Event
			WebiUserLogged::dispatch($user, request()->ip());

			return $this->jsonResponse('Authenticated.', [
				'user' => $user,
			]);
		} else {
			throw new WebiException('Invalid credentials.');
		}
	}
}
