<?php

namespace Tests\Webi;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Webi\Enums\User\UserRole;
use Webi\Events\WebiUserCreated;
use Webi\Listeners\WebiUserCreatedNotification;
use Webi\Mail\RegisterMail;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiRegisterPLTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * @test
	 */
	function db_create_user()
	{
		$user = User::factory()->role(UserRole::USER)->create();

		$this->assertNotNull($user);

		$this->assertDatabaseHas('users', [
			'name' => $user->name,
			'email' => $user->email,
			'role' => 'user',
		]);
	}

	/**
	 * @test
	 */
	function db_create_worker()
	{
		$user = User::factory()->role(UserRole::WORKER)->create();

		$this->assertNotNull($user);

		$this->assertDatabaseHas('users', [
			'name' => $user->name,
			'email' => $user->email,
			'role' => 'worker',
		]);
	}

	/**
	 * @test
	 */
	function db_create_admin()
	{
		$user = User::factory()->role(UserRole::ADMIN)->create();

		$this->assertNotNull($user);

		$this->assertDatabaseHas('users', [
			'name' => $user->name,
			'email' => $user->email,
			'role' => 'admin',
		]);
	}

	/** @test */
	function http_create_user()
	{
		$pass = 'password123';

		$user = User::factory()->make();

		// Events testing
		Event::fake([MessageSent::class]);

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => $user->email,
			'password' => $pass,
			'password_confirmation' => $pass,
		]);

		$res->assertStatus(201)->assertJson(['created' => true]);

		$this->assertDatabaseHas('users', [
			'name' => $user->name,
			'email' => $user->email,
		]);

		$db_user = User::where('email', $user->email)->first();

		$this->assertTrue(Hash::check($pass, $db_user->password));

		Event::assertDispatched(MessageSent::class, function ($e) {
			$html = $e->message->getHtmlBody();
			$this->assertStringContainsString("/activate", $html);
			$this->assertMatchesRegularExpression('/\/activate\/[0-9]+\/[a-z0-9]+\?locale=[a-z]{2}"/i', $html);
			return true;
		});

		// Mail testing
		Mail::fake();

		$res = $this->postJson('/web/api/register', [
			'name' => 'Mr. ' . $user->name,
			'email' => 'test' . $user->email,
			'password' => $pass,
			'password_confirmation' => $pass,
		]);

		$res->assertStatus(201)->assertJson(['created' => true]);

		// Mail send test
		Mail::assertSent(RegisterMail::class);

		// Event listeners
		Event::assertListening(
			WebiUserCreated::class,
			WebiUserCreatedNotification::class
		);
	}

	/** @test */
	function email_duplicated_error()
	{
		app()->setLocale('pl');

		$user = User::factory()->create();

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => $user->email,
			'password' => 'password123',
			'password_confirmation' => 'password123',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'Taki adres email już występuje.'
		]);
	}

	/** @test */
	function register_user_error_name()
	{
		app()->setLocale('pl');

		$user = User::factory()->make();

		$res = $this->postJson('/web/api/register', [
			'name' => '',
			'email' => $user->email,
			'password' => 'password123',
			'password_confirmation' => 'password123',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'Pole imię jest wymagane.'
		]);
	}

	function test_error_email()
	{
		app()->setLocale('pl');

		$user = User::factory()->make();

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => '',
			'password' => 'password123',
			'password_confirmation' => 'password123',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'Pole adres email jest wymagane.'
		]);
	}

	function test_error_password()
	{
		app()->setLocale('pl');

		$user = User::factory()->make();

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => $user->email,
			'password' => '',
			'password_confirmation' => 'password123',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'Pole hasło jest wymagane.'
		]);
	}

	function test_error_password_confirmation()
	{
		app()->setLocale('pl');

		$user = User::factory()->make();

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => $user->email,
			'password' => 'password123',
			'password_confirmation' => '',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'Potwierdzenie pola hasło nie zgadza się.'
		]);
	}
}
