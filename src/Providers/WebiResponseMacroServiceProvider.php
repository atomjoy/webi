<?php

namespace Webi\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Webi\Services\Webi;

class WebiResponseMacroServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Response::macro('errors', function ($data, $code = 422, $headers = []) {
			return (new Webi())->jsonResponse($data, $code, $headers);
		});

		Response::macro('success', function ($data, $code = 200, $headers = []) {
			return (new Webi())->jsonResponse($data, $code, $headers);
		});
	}
}
