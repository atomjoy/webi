<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

			$request->session()->regenerate();

			$user = Auth::user();

			if (!$user instanceof User) {
				return response()->errors('Invalid credentials.');
			}

			if (empty($user->email_verified_at)) {
				return response()->errors('The account has not been activated.');
			}

			WebiUserLogged::dispatch($user, request()->ip());

			return response()->success([
				'message' => trans('Authenticated.'),
				'user' => $user,
			]);
		} else {
			return response()->errors('Invalid credentials.');
		}
	}
}
