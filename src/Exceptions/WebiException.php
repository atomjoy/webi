<?php

namespace Webi\Exceptions;

use Exception;

class WebiException extends Exception
{
	public function __construct(string $message = "", int $code = 422, Throwable|null $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
