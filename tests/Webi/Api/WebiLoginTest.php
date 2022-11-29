<?php

namespace Tests\Webi\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Webi\Events\WebiUserLogged;
use Webi\Listeners\WebiUserLoggedNotification;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiLoginTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function login_user_method()
	{
		$res = $this->getJson('/web/api/login');

		$res->assertStatus(400)->assertJson([
			'alert' => [
				'message' => 'Invalid api route path or request method.',
				'type' => 'error',
			]
		]);
	}

	/** @test */
	function login_user_errors()
	{
		$user = User::factory()->create();

		$res = $this->postJson('/web/api/login', [
			'email' => '',
			'password' => 'password123',
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'The email field is required.',
				'type' => 'danger',
			]
		]);

		$res = $this->postJson('/web/api/login', [
			'email' => $user->email . 'error###',
			'password' => 'password123',
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'The email must be a valid email address.',
				'type' => 'danger',
			]
		]);

		$res = $this->postJson('/web/api/login', [
			'email' => $user->email,
			'password' => '',
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'The password field is required.',
				'type' => 'danger',
			]
		]);

		$res = $this->postJson('/web/api/login', [
			'email' => $user->email,
			'password' => 'password',
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'The password must be at least 11 characters.',
				'type' => 'danger',
			]
		]);
	}

	/** @test */
	function login_user()
	{
		Auth::logout();

		$user = User::factory()->create([
			'password' => Hash::make('hasło1233456')
		]);

		$this->assertDatabaseHas('users', [
			'name' => $user->name,
			'email' => $user->email,
		]);

		$res = $this->postJson('/web/api/login', [
			'email' => $user->email,
			'password' => 'hasło1233456'
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Authenticated.',
				'type' => 'success',
			]
		])->assertJsonStructure([
			'bag' => ['user']
		])->assertJsonPath('bag.user.email', $user->email);

		$this->assertNotNull($res['alert']);
	}

	/** @test */
	function login_remember_me()
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
		]);

		$token = User::where('email', $user->email)->first()->remember_token;

		$res = $this->withCookie('webi_token', $token)->get('/web/api/logged');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Authenticated.',
				'type' => 'success',
			]
		])->assertCookie('webi_token');
	}

	/** @test */
	function login_user_events()
	{
		Event::fake();

		$user = User::factory()->create();

		$res = $this->postJson('/web/api/login', [
			'email' => $user->email,
			'password' => 'password123',
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Authenticated.',
				'type' => 'success',
			]
		])->assertJsonStructure([
			'bag' => ['user']
		])->assertJsonPath('bag.user.email', $user->email);;

		// Event listeners
		Event::assertListening(
			WebiUserLogged::class,
			WebiUserLoggedNotification::class,
		);
	}
}
