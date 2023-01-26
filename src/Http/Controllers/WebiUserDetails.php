<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webi\Traits\Http\HasJsonResponse;

class WebiUserDetails extends Controller
{
	// use HasJsonResponse;

	function index(Request $request)
	{
		return response()->success([
			'message' => trans('User details.'),
			'user' => request()->user(),
			'ip' => request()->ip()
		]);
	}
}
