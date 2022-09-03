<?php

namespace Webi\Traits\Http;

trait HasJsonResponse
{
	function jsonResponse($message, $data = null, $code = 200)
	{
		if ((int) $code < 100 || (int) $code > 599) {
			$code = 422;
		}

		if (config('webi.settings.translate_response') == true) {
			$message = trans($message);
		}

		return response()->json([
			'message' => $message,
			'code' => $code,
			'data' => $data,
		], $code);
	}
}
