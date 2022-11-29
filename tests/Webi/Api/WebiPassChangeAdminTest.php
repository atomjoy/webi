<?php

namespace Tests\Webi\Api;

use Illuminate\Support\Facades\Auth;
use Webi\Enums\User\UserRole;
use Webi\Traits\Tests\AuthenticatedTestCase;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiPassChangeAdminTest extends AuthenticatedTestCase
{
	protected UserRole $authWithRole = UserRole::ADMIN;

	/** @test */
	function logged_as_admin()
	{
		$this->assertSame($this->user->role, UserRole::ADMIN);
	}

	/** @test */
	function logged_user_data()
	{
		$res = $this->get('web/api/test/admin');

		$res->assertStatus(200)->assertJsonStructure([
			'alert' => ['message', 'type'],
			'bag' => ['ip', 'user']
		]);
	}

	/** @test */
	function change_logged_user_password()
	{
		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'Password1234X',
			'password' => 'Password1234#',
			'password_confirmation' => 'Password1234#'
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Invalid current password.',
				'type' => 'danger',
			]
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'password',
			'password_confirmation' => 'password123'
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'The password must be at least 11 characters.',
				'type' => 'danger',
			]
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'password1234',
			'password_confirmation' => 'password'
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'The password must contain at least one uppercase and one lowercase letter.',
				'type' => 'danger',
			]
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'Password1234',
			'password_confirmation' => 'Password1234'
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'The password must contain at least one symbol.',
				'type' => 'danger',
			]
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'Passwordoooo#',
			'password_confirmation' => 'Passwordoooo#'
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'The password must contain at least one number.',
				'type' => 'danger',
			]
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'Password1234#',
			'password_confirmation' => 'Password1234#1'
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'The password confirmation does not match.',
				'type' => 'danger',
			]
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'Password1234#',
			'password_confirmation' => 'Password1234#'
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Password has been updated.',
				'type' => 'success',
			]
		]);

		Auth::logout();

		$res = $this->postJson('/web/api/login', [
			'email' => $this->user->email,
			'password' => 'Password1234#'
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Authenticated.',
				'type' => 'success',
			]
		]);

		$this->assertNotNull($res['alert']);
	}

	/** @test */
	function dont_allow_change_not_logged_user_password()
	{
		Auth::logout();

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'password1234',
			'password_confirmation' => 'password1234'
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Unauthenticated.',
				'type' => 'danger',
			]
		]);
	}

	/** @test */
	function user_logout()
	{
		$res = $this->getJson('/web/api/logout');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Logged out.',
				'type' => 'success',
			]
		]);
	}
}
