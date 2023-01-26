<?php

namespace Webi\Traits\Http;

trait HasJsonResponse
{
	function jsonResponse($data, $code = 200, $headers = [])
	{
		return response()->json($this->strToArray($data), $code, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	function strToArray($data)
	{
		return !is_array($data) ? ['message' => trans($data)] : $data;
	}
}
