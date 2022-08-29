<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebiCsrf extends Controller
{
	function index(Request $request)
	{
		$request->session()->regenerateToken();

		session(['webi_cnt' => session('webi_cnt') + 1]);

		return response([
			'message' => trans('Csrf token created.'),
			'counter' => session('webi_cnt'),
			'locale' => app()->getLocale(),
			'session_locale' => session('locale'),
		]);
	}
}
