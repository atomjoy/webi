<?php

namespace Tests\Webi\Api;

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
			'alert' => [
				'message' => 'Authenticated.',
				'type' => 'success',
			]
		])->assertJsonStructure([
			'bag' => ['user']
		])->assertJsonPath('bag.user.email', $user->email);

		$token = 'token12345';

		$res = $this->withCookie('webi_token', $token)->get('/web/api/logged');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Authenticated.',
				'type' => 'success',
			]
		])->assertJsonStructure([
			'bag' => ['user']
		])->assertJsonPath('bag.user.email', $user->email)->assertCookie('webi_token');
	}

	/** @test */
	function logged_not_authenticated()
	{
		Auth::logout();

		$res = $this->getJson('/web/api/logged');

		$res->assertStatus(422)->assertJson([
			'alert' => [
				'message' => 'Not authenticated.',
				'type' => 'danger',
			]
		])->assertJsonStructure([
			'bag' => ['user']
		])->assertJsonPath('data.user', null);
	}
}
