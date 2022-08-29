<?php

namespace Webi\Enums\User;

enum UserRole: string
{
	case USER = 'user';
	case WORKER = 'worker';
	case ADMIN = 'admin';

	/**
	 * Convert enum to array
	 */
	public static function toArray(): array
	{
		return array_column(self::cases(), 'name');
	}

	/**
	 * Convert string to UserRole
	 *
	 * @param string $value
	 */
	public static function fromString(string $value): UserRole
	{
		return self::from($value);
	}
}
