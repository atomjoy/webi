<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WebiPassConfirm extends Controller
{
	function index(Request $request)
	{
		if (Auth::check()) {
			if (Hash::check($request->input('password'), Auth::user()->password)) {
				return response()->success([
					'message' => trans('Authenticated.'),
					'user' => Auth::user()
				]);
			} else {
				return response()->errors('Invalid current password.');
			}
		}

		return response()->errors([
			'message' => trans('Unauthenticated.'),
			'user' => null
		]);
	}
}
