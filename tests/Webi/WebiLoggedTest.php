<?php

namespace Tests\Webi;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiLoggedTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function logged_with_remember_me_cookie()
	{
		$user = User::factory()->create();

		$res = $this->postJson('/web/api/login', [
			'email' => $user->email,
			'password' => 'password123',
			'remember_me' => 1,
		]);

		$res->assertStatus(200)->assertJson([
			'message' => 'Authenticated.'
		]);

		$token = User::where('email', $user->email)->first()->remember_token;

		$res = $this->withCookie('_remeber_token', $token)->get('/web/api/logged');

		$res->assertStatus(200)->assertJson([
			'message' => 'Authenticated via remember me.'
		]);
	}

	/** @test */
	function logged_not_authenticated()
	{
		Auth::logout();

		$res = $this->getJson('/web/api/logged');

		$res->assertStatus(422)->assertJson([
			'message' => 'Not authenticated.'
		]);
	}
}
