<?php

namespace Webi\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Webi\Traits\Http\HasJsonResponse;

class WebiLocale extends Controller
{
	use HasJsonResponse;

	function index($locale)
	{
		if (strlen($locale) == 2) {
			app()->setLocale($locale);

			session(['locale' => app()->getLocale()]);

			return $this->jsonResponse('Locale has been changed.', [
				'locale' => app()->getLocale(),
			]);
		}

		throw new Exception('Locale has not been changed.', 422);
	}
}
