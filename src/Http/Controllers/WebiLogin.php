<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

			$request->session()->regenerate();

			$user = Auth::user();

			if (!$user instanceof User) {
				throw new WebiException('Invalid credentials.');
			}

			if (empty($user->email_verified_at)) {
				throw new WebiException('The account has not been activated.');
			}

			WebiUserLogged::dispatch($user, request()->ip());

			return $this->jsonResponse('Authenticated.', [
				'user' => $user,
			]);
		} else {
			throw new WebiException('Invalid credentials.');
		}
	}
}
