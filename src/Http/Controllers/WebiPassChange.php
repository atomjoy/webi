<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Webi\Exceptions\WebiException;
use Webi\Http\Requests\WebiChangePasswordRequest;

class WebiPassChange extends Controller
{
	function index(WebiChangePasswordRequest $request)
	{
		$valid = $request->validated();

		if (Hash::check($valid['password_current'], Auth::user()->password)) {

			User::where([
				'email' => $request->user()->email
			])->update([
				'password' => Hash::make($request->input('password')),
				'ip' => $request->ip()
			]);

			return response()->success('Password has been updated.');
		} else {
			throw new WebiException('Invalid current password.');
		}

		throw new WebiException('Password has not been updated.');
	}
}
