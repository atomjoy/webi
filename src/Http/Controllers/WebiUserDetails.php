<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebiUserDetails extends Controller
{
	function index(Request $request)
	{
		return response()->json([
			'message' => request()->user(),
			'ip' => request()->ip()
		]);
	}
}
