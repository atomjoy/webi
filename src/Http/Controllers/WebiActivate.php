<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Webi\Http\Requests\WebiActivateRequest;

class WebiActivate extends Controller
{
	function index(WebiActivateRequest $request)
	{
		$valid = $request->validated();
		$user = null;

		$user = User::where('id', (int) $valid['id'])->first();

		if (!$user instanceof User) {
			return response()->errors("Invalid activation code.");
		}

		if (!empty($user->email_verified_at)) {
			return response()->success('The email address has already been confirmed.');
		}

		if ($user->code == strip_tags($valid['code'])) {
			$user->update(['email_verified_at' => now()]);

			return response()->success('Email has been confirmed.');
		}

		return response()->errors("Email has not been activated.");
	}
}
