<?php

namespace Webi\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Webi\Exceptions\WebiException;
use Webi\Traits\Http\HasJsonResponse;

class WebiLocale extends Controller
{
	// use HasJsonResponse;

	function index($locale)
	{
		if (strlen($locale) == 2) {
			app()->setLocale($locale);

			session(['locale' => app()->getLocale()]);

			return response()->success('Locale has been changed.', [
				'locale' => app()->getLocale(),
			]);
		}

		throw new WebiException('Locale has not been changed.');
	}
}
