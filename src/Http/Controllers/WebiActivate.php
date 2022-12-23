<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Webi\Exceptions\WebiException;
use Webi\Http\Requests\WebiActivateRequest;
use Webi\Traits\Http\HasJsonResponse;

class WebiActivate extends Controller
{
	// use HasJsonResponse;

	function index(WebiActivateRequest $request)
	{
		$valid = $request->validated();
		$user = null;

		$user = User::where('id', (int) $valid['id'])->first();

		if (!$user instanceof User) {
			throw new WebiException("Invalid activation code.");
		}

		if (!empty($user->email_verified_at)) {
			return response()->success('The email address has already been confirmed.');
		}

		if ($user->code == strip_tags($valid['code'])) {
			$user->update(['email_verified_at' => now()]);

			return response()->success('Email has been confirmed.');
		}

		throw new WebiException("Email has not been activated.");
	}
}
