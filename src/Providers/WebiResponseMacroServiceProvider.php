<?php

namespace Webi\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

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
			if (config('webi.settings.translate_response') == true) {
				$message = trans($message);
			}

			return response()->json([
				'alert' => [
					'message' => $message,
					'type' => $alert_type,
				],
				'bag' => $data,
			], $code, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		});

		Response::macro('success', function ($message, $data = null, $code = 200, $alert_type = 'success', $headers = []) {
			if (config('webi.settings.translate_response', false) == true) {
				$message = trans($message);
			}

			return response()->json([
				'alert' => [
					'message' => $message,
					'type' => $alert_type,
				],
				'bag' => $data,
			], $code, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		});
	}
}
