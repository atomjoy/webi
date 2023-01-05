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
		Response::macro('errors', function ($message, $data = null, $code = 422, $alert_type = 'danger', $headers = []) {
			return (new Webi())->jsonResponse($message, $data, $code, $alert_type, $headers);
		});

		Response::macro('success', function ($message, $data = null, $code = 200, $alert_type = 'success', $headers = []) {
			return (new Webi())->jsonResponse($message, $data, $code, $alert_type, $headers);
		});
	}
}
