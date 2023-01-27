<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebiUserDetails extends Controller
{
	function index(Request $request)
	{
		return response()->success([
			'message' => trans('User details.'),
			'user' => request()->user(),
			'ip' => request()->ip()
		]);
	}
}
