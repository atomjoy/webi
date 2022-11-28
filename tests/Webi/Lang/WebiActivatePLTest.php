<?php

namespace Tests\Webi\Lang;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiActivatePLTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function invalid_activation_user_id()
	{
		app()->setLocale('pl');

		$user = User::factory()->create(['email_verified_at' => null]);

		$this->assertDatabaseHas('users', [
			'email' => $user->email,
			'code' => $user->code,
		]);

		// min:1
		$res = $this->get('/web/api/activate/0/' . $user->code);

		// Only numbers
		$res->assertStatus(200)->assertJson([
			'message' => 'Pole id musi być nie mniejsze od 1.'
		]);

		// Invalid number id
		$res = $this->get('/web/api/activate/error123/' . $user->code);

		// Only numbers
		$res->assertStatus(200)->assertJson([
			'message' => 'Pole id musi być liczbą.'
		]);

		// Invalid user id
		$res = $this->get('/web/api/activate/123/' . $user->code);

		$res->assertStatus(200)->assertJson([
			'message' => 'Niepoprawny kod aktywacyjny.'
		]);
	}

	/** @test */
	function invalid_activation_user_code()
	{
		app()->setLocale('pl');

		$user = User::factory()->create(['email_verified_at' => null]);

		$this->assertModelExists($user);

		$this->assertDatabaseHas('users', [
			'email' => $user->email,
			'code' => $user->code,
		]);

		// min:6
		$res = $this->get('/web/api/activate/' . $user->id . '/er123');

		$res->assertStatus(200)->assertJson([
			'message' => 'Pole code musi mieć przynajmniej 6 znaków.'
		]);

		// max:30
		$res = $this->get('/web/api/activate/' . $user->id . '/' . md5('tolongcode'));

		$res->assertStatus(200)->assertJson([
			'message' => 'Pole code nie może być dłuższe niż 30 znaków.'
		]);

		// Code valid but not exists
		$res = $this->get('/web/api/activate/' . $user->id . '/errorcode123');

		$res->assertStatus(200)->assertJson([
			'message' => 'Nie można potwierdzić adresu email.'
		]);
	}

	/** @test */
	function activate_user()
	{
		app()->setLocale('pl');

		$user = User::factory()->create(['email_verified_at' => null]);

		$this->assertDatabaseHas('users', [
			'email' => $user->email,
			'code' => $user->code,
		]);

		// Activated
		$res = $this->get('/web/api/activate/' . $user->id . '/' . $user->code);

		$res->assertStatus(200)->assertJson([
			'message' => 'Adres email został potwierdzony.'
		]);

		// Exists
		$res = $this->get('/web/api/activate/' . $user->id . '/' . $user->code);

		$res->assertStatus(200)->assertJson([
			'message' => 'Adres email jest już potwierdzony.'
		]);

		// Is Activated
		$db_user = User::where('email', $user->email)->first();

		$this->assertNotNull($db_user->email_verified_at);
	}
}
