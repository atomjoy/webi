<?php

namespace Webi\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\User;
use Webi\Http\Requests\WebiActivateRequest;

class WebiActivate extends Controller
{
	function index(WebiActivateRequest $request)
	{
		$valid = $request->validated();
		$user = null;

		try {
			$user = User::where('id', (int) $valid['id'])->first();
		} catch (Exception $e) {
			report($e);
			throw new Exception("Database error.", 422);
		}

		if (!$user instanceof User) {
			throw new Exception("Invalid activation code.", 422);
		}

		if (!empty($user->email_verified_at)) {
			return response()->json([
				'message' => trans('The email address has already been confirmed.')
			]);
		}

		try {
			if ($user->code == strip_tags($valid['code'])) {
				$user->update(['email_verified_at' => now()]);

				return response()->json([
					'message' => trans('Email has been confirmed.')
				]);
			}
		} catch (Exception $e) {
			report($e);
			throw new Exception("Database error.", 422);
		}

		throw new Exception("Email has not been activated.", 422);
	}
}
