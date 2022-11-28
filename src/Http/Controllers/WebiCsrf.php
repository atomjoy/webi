<?php

namespace Webi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webi\Traits\Http\HasJsonResponse;

class WebiCsrf extends Controller
{
	use HasJsonResponse;

	function index(Request $request)
	{
		$request->session()->regenerateToken();

		session(['webi_cnt' => session('webi_cnt') + 1]);

		return $this->jsonResponse('Csrf token created.', [
			'counter' => session('webi_cnt'),
			'locale' => app()->getLocale(),
			'session_locale' => session('locale'),
		]);
	}
}
