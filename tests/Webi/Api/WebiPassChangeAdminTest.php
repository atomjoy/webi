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
			'data' => ['ip', 'user']
		]);
	}

	/** @test */
	function change_logged_user_password()
	{
		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123X',
			'password' => 'password1234',
			'password_confirmation' => 'password1234'
		]);

		$res->assertStatus(422)->assertJson([
			'message' => 'Invalid current password.'
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'password',
			'password_confirmation' => 'password123'
		]);

		$res->assertStatus(422)->assertJson([
			'message' => 'The password must be at least 11 characters.'
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'password1234',
			'password_confirmation' => 'password'
		]);

		$res->assertStatus(422)->assertJson([
			'message' => 'The password confirmation does not match.'
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'password1234',
			'password_confirmation' => 'password1234'
		]);

		$res->assertStatus(200)->assertJson([
			'message' => 'Password has been updated.'
		]);

		Auth::logout();

		$res = $this->postJson('/web/api/login', [
			'email' => $this->user->email,
			'password' => 'password1234'
		]);

		$res->assertStatus(200)->assertJson([
			'message' => 'Authenticated.'
		]);

		$this->assertNotNull($res['message']);
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

		$res->assertStatus(401)->assertJson([
			'message' => 'Unauthenticated.'
		]);
	}

	/** @test */
	function user_logout()
	{
		$res = $this->getJson('/web/api/logout');

		$res->assertStatus(200)->assertJson([
			'message' => 'Logged out.'
		]);
	}
}
