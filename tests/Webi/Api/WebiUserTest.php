<?php

namespace Tests\Webi\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiUserTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function check_user_model_fillable()
	{
		$expected_fillable = [
			'name',
			'email',
			'password',
			'email_verified_at',
			'remember_token',
			'newsletter_on',
			'mobile_prefix',
			'mobile',
			'username',
			'location',
			'website',
			'locale',
			'image',
			'role',
			'code',
			'ip',
		];

		$user = User::factory()->create();

		$model_fillable = $user->getFillable();

		foreach ($expected_fillable as $fillable_name) {
			if (!in_array($fillable_name, $model_fillable)) {
				$this->assertTrue(
					false,
					'User model fillable array key ' . $fillable_name . ' does not exists.'
				);
			}
		}

		$this->assertTrue(true);
	}
}
