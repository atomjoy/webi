<?php

namespace Webi\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;

class WebiLocale extends Controller
{
	function index($locale)
	{
		if (strlen($locale) == 2) {
			app()->setLocale($locale);

			session(['locale' => app()->getLocale()]);

			return response()->json([
				'message' => trans('Locale has been changed.'),
				'locale' => app()->getLocale(),
			], 200);
		}

		throw new Exception('Locale has not been changed.', 422);
	}
}
