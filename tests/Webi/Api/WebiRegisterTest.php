<?php

namespace Tests\Webi\Api;

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

class WebiRegisterTest extends TestCase
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
			'name' => htmlspecialchars($user->name, ENT_QUOTES),
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
			'name' => htmlspecialchars($user->name, ENT_QUOTES),
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
			'name' => htmlspecialchars($user->name, ENT_QUOTES),
			'email' => $user->email,
			'role' => 'admin',
		]);
	}

	/** @test */
	function http_validate_user()
	{
		$user = User::factory()->make();

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => $user->email,
			'password' => 'Password1234#',
			'password_confirmation' => 'Password1234#1',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'The password confirmation does not match.'
		]);

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => $user->email,
			'password' => 'Password1234',
			'password_confirmation' => 'Password1234',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'The password must contain at least one symbol.'
		]);

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => $user->email,
			'password' => 'password1234#',
			'password_confirmation' => 'password1234#',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'The password must contain at least one uppercase and one lowercase letter.'
		]);

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => $user->email,
			'password' => 'Passwordoooo#',
			'password_confirmation' => 'Passwordoooo#',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'The password must contain at least one number.'
		]);
	}

	/** @test */
	function http_create_user()
	{
		$pass = 'Password1234#';

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
			'name' => htmlspecialchars($user->name, ENT_QUOTES),
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
		$user = User::factory()->create();

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => $user->email,
			'password' => 'password123',
			'password_confirmation' => 'password123',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'The email has already been taken.'
		]);
	}

	/** @test */
	function register_user_error_name()
	{
		$user = User::factory()->make();

		$res = $this->postJson('/web/api/register', [
			'name' => '',
			'email' => $user->email,
			'password' => 'password123',
			'password_confirmation' => 'password123',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'The name field is required.'
		]);
	}

	function test_error_email()
	{
		$user = User::factory()->make();

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => '',
			'password' => 'password123',
			'password_confirmation' => 'password123',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'The email field is required.'
		]);
	}

	function test_error_password()
	{
		$user = User::factory()->make();

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => $user->email,
			'password' => '',
			'password_confirmation' => 'password123',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'The password field is required.'
		]);
	}

	function test_error_password_confirmation()
	{
		$user = User::factory()->make();

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => $user->email,
			'password' => 'password1234#',
			'password_confirmation' => '',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'The password must contain at least one uppercase and one lowercase letter.'
		]);

		$res = $this->postJson('/web/api/register', [
			'name' => $user->name,
			'email' => $user->email,
			'password' => 'Password1234#',
			'password_confirmation' => 'Password1234#1',
		]);

		$res->assertStatus(422)->assertJsonMissing(['created'])->assertJson([
			'message' => 'The password confirmation does not match.'
		]);
	}
}
