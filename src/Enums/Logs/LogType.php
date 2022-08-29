<?php

namespace Webi\Enums\Log;

enum LogType: string
{
	case CREATED = 'CREATED';
	case LOGGED = 'LOGGED';

	/**
	 * Convert enum to array
	 */
	public static function toArray(): array
	{
		return array_column(self::cases(), 'name');
	}
}
