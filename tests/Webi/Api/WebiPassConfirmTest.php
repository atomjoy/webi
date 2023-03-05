<?php

namespace Tests\Webi\Api;

use Illuminate\Support\Facades\Auth;
use Webi\Traits\Tests\AuthenticatedTestCase;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiPassConfirmTest extends AuthenticatedTestCase
{
	/** @test */
	function confirm_logged_user_password()
	{
		$res = $this->postJson('/web/api/confirm-password', [
			'password' => 'password123##'
		]);

		$res->assertStatus(422)->assertJson([
			'message' => 'Invalid current password.'
		]);

		$res = $this->postJson('/web/api/confirm-password', [
			'password' => 'password123'
		]);

		$res->assertStatus(200)->assertJson([
			'message' => 'Authenticated.'
		]);

		Auth::logout();

		$res = $this->postJson('/web/api/confirm-password', [
			'password' => 'password123'
		]);

		$res->assertStatus(401)->assertJson([
			'message' => 'Unauthenticated.'
		]);
	}
}
