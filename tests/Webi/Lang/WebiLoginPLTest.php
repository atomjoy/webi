<?php

namespace Tests\Webi\Lang;

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

class WebiLoginPLTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function login_user_emethod()
	{
		app()->setLocale('pl');

		$res = $this->getJson('/web/api/login');

		$res->assertStatus(400)->assertJson([
			'alert' => [
				'message' => 'Niepoprawny adres url lub metoda http.',
				'type' => 'error',
			],
		]);
	}

	/** @test */
	function login_user_errors()
	{
		app()->setLocale('pl');

		$user = User::factory()->create();

		$res = $this->postJson('/web/api/login', [
			'email' => '',
			'password' => 'password123',
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Pole adres email jest wymagane.',
				'type' => 'danger',
			],
		]);

		$res = $this->postJson('/web/api/login', [
			'email' => $user->email . 'error###',
			'password' => 'password123',
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Pole adres email nie jest poprawnym adresem e-mail.',
				'type' => 'danger',
			],
		]);

		$res = $this->postJson('/web/api/login', [
			'email' => $user->email,
			'password' => '',
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Pole hasło jest wymagane.',
				'type' => 'danger',
			],
		]);

		$res = $this->postJson('/web/api/login', [
			'email' => $user->email,
			'password' => 'password',
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Pole hasło musi mieć przynajmniej 11 znaków.',
				'type' => 'danger',
			],
		]);
	}

	/** @test */
	function login_user()
	{
		Auth::logout();

		app()->setLocale('pl');

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
				'message' => 'Zalogowany.',
				'type' => 'success',
			],
		]);

		$this->assertNotNull($res['alert']);
	}

	/** @test */
	function login_remember_me()
	{
		app()->setLocale('pl');

		$user = User::factory()->create();

		$res = $this->postJson('/web/api/login', [
			'email' => $user->email,
			'password' => 'password123',
			'remember_me' => 1,
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Zalogowany.',
				'type' => 'success',
			],
		]);

		$token = User::where('email', $user->email)->first()->remember_token;

		$res = $this->withCookie('webi_token', $token)->get('/web/api/logged');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Zalogowany.',
				'type' => 'success',
			],
		]);
	}

	/** @test */
	function login_user_events()
	{
		app()->setLocale('pl');

		Event::fake();

		$user = User::factory()->create();

		$res = $this->postJson('/web/api/login', [
			'email' => $user->email,
			'password' => 'password123',
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Zalogowany.',
				'type' => 'success',
			],
		]);

		// Event listeners
		Event::assertListening(
			WebiUserLogged::class,
			WebiUserLoggedNotification::class,
		);
	}
}
