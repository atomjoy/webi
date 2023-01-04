<?php

namespace Webi\Traits\Http;

trait HasJsonResponse
{
	function jsonResponse($message, $data = null, $code = 200, $alert_type = 'success', $headers = [])
	{
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
	}
}
