<?php

namespace Tests\Webi\Api;

use Tests\TestCase;
use Webi\Enums\User\UserRole;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiUserRoleTest extends TestCase
{
	/** @test */
	function user_role_enum_values()
	{
		$this->assertTrue(UserRole::USER->value == 'user');

		$this->assertTrue(UserRole::ADMIN->value == 'admin');

		$this->assertTrue(UserRole::WORKER->value == 'worker');
	}

	/** @test */
	function user_role_enum_methods()
	{
		$this->assertTrue(UserRole::fromString('user') == UserRole::USER);

		$this->assertTrue(count(UserRole::toArray()) == 3);

		$this->assertTrue(count(UserRole::cases()) == 3);
	}
}
