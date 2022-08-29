<?php

namespace Webi\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Webi\Http\Requests\WebiChangePasswordRequest;

class WebiPassChange extends Controller
{
	function index(WebiChangePasswordRequest $request)
	{
		$valid = $request->validated();

		if (Hash::check($valid['password_current'], Auth::user()->password)) {
			try {
				User::where([
					'email' => $request->user()->email
				])->update([
					'password' => Hash::make($request->input('password')),
					'ip' => $request->ip()
				]);

				return response()->json([
					'message' => trans('Password has been updated.')
				]);
			} catch (Exception $e) {
				report($e);
				throw new Exception('Database error.', 422);
			}
		} else {
			throw new Exception('Invalid current password.', 422);
		}

		throw new Exception('Password has not been updated.', 422);
	}
}
