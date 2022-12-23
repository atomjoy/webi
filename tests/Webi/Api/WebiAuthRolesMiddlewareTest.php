<?php

namespace Tests\Webi\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webi\Enums\User\UserRole;
use Webi\Http\Middleware\WebiAuthRoles;
use Webi\Traits\Tests\AuthenticatedTestCase;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiAuthRolesMiddlewareTest extends AuthenticatedTestCase
{
	protected UserRole $authWithRole = UserRole::ADMIN;

	/** @test */
	function auth_roles_middleware()
	{
		$this->assertSame($this->user->role, UserRole::ADMIN);

		// Is called
		$called = false;

		// Given we have a request
		$request = new Request();
		$request->merge(['title' => 'some title']);

		// Test valid roles middleware
		(new WebiAuthRoles())->handle($request, function ($request) use (&$called) {
			$called = true;
			$this->assertEquals('some title', $request->title);
		}, 'admin'); // roles without webi-role:

		$this->assertTrue($called);

		// Test invalid roles middleware
		try {
			(new WebiAuthRoles())->handle($request, function ($request) {
				$this->assertEquals('some title', $request->title);
			}, 'user|worker'); // roles without webi-role:
		} catch (Exception $e) {
			$this->assertEquals($e->getMessage(), 'Unauthorized Role.');
		}
	}

	/** @test */
	function auth_roles_only_logged_users()
	{
		$res = $this->get('web/api/test/admin');

		$res->assertStatus(200);

		$res = $this->get('web/api/test/worker');

		$res->assertStatus(200);

		$res = $this->get('web/api/test/user');

		$res->assertStatus(200);

		Auth::logout();

		$res = $this->get('web/api/test/admin');

		$res->assertStatus(401);

		$res = $this->get('web/api/test/worker');

		$res->assertStatus(401);

		$res = $this->get('web/api/test/user');

		$res->assertStatus(401);
	}
}
