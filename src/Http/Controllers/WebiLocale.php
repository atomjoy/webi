<?php

namespace Webi\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Webi\Exceptions\WebiException;

class WebiLocale extends Controller
{
	function index($locale)
	{
		if (strlen($locale) == 2) {
			app()->setLocale($locale);

			session(['locale' => app()->getLocale()]);

			return response()->success([
				'message' => trans('Locale has been changed.'),
				'locale' => app()->getLocale(),
			]);
		}

		throw new WebiException('Locale has not been changed.');
	}
}
