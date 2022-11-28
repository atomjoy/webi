<?php

namespace Tests\Webi\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiActivateTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function invalid_activation_user_id()
	{
		$user = User::factory()->create(['email_verified_at' => null]);

		$this->assertDatabaseHas('users', [
			'email' => $user->email,
			'code' => $user->code,
		]);

		// min:1
		$res = $this->get('/web/api/activate/0/' . $user->code);

		// Only numbers
		$res->assertStatus(200)->assertJson([
			'message' => 'The id must be at least 1.'
		]);

		// Invalid number id
		$res = $this->get('/web/api/activate/error123/' . $user->code);

		// Only numbers
		$res->assertStatus(200)->assertJson([
			'message' => 'The id must be a number.'
		]);

		// Invalid user id
		$res = $this->get('/web/api/activate/123/' . $user->code);

		$res->assertStatus(200)->assertJson([
			'message' => 'Invalid activation code.'
		]);
	}

	/** @test */
	function invalid_activation_user_code()
	{
		$user = User::factory()->create(['email_verified_at' => null]);

		$this->assertModelExists($user);

		$this->assertDatabaseHas('users', [
			'email' => $user->email,
			'code' => $user->code,
		]);

		// min:6
		$res = $this->get('/web/api/activate/' . $user->id . '/er123');

		$res->assertStatus(200)->assertJson([
			'message' => 'The code must be at least 6 characters.'
		]);

		// max:30
		$res = $this->get('/web/api/activate/' . $user->id . '/' . md5('tolongcode'));

		$res->assertStatus(200)->assertJson([
			'message' => 'The code must not be greater than 30 characters.'
		]);

		// Code valid but not exists
		$res = $this->get('/web/api/activate/' . $user->id . '/errorcode123');

		$res->assertStatus(200)->assertJson([
			'message' => 'Email has not been activated.'
		]);
	}

	/** @test */
	function activate_user()
	{
		$user = User::factory()->create(['email_verified_at' => null]);

		$this->assertDatabaseHas('users', [
			'email' => $user->email,
			'code' => $user->code,
		]);

		// Activated
		$res = $this->get('/web/api/activate/' . $user->id . '/' . $user->code);

		$res->assertStatus(200)->assertJson([
			'message' => 'Email has been confirmed.'
		]);

		// Exists
		$res = $this->get('/web/api/activate/' . $user->id . '/' . $user->code);

		$res->assertStatus(200)->assertJson([
			'message' => 'The email address has already been confirmed.'
		]);

		// Is Activated
		$db_user = User::where('email', $user->email)->first();

		$this->assertNotNull($db_user->email_verified_at);
	}
}
