<?php

namespace Tests\Webi\Lang;

use Illuminate\Support\Facades\Auth;
use Webi\Enums\User\UserRole;
use Webi\Traits\Tests\AuthenticatedTestCase;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiPassChangeUserPLTest extends AuthenticatedTestCase
{
	protected UserRole $authWithRole = UserRole::USER;

	/** @test */
	function logged_as_admin()
	{
		$this->assertSame($this->user->role, UserRole::USER);
	}

	/** @test */
	function logged_user_data()
	{
		$res = $this->get('web/api/test/user');

		$res->assertStatus(200)->assertJsonStructure(['ip', 'user']);
	}

	/** @test */
	function change_logged_user_password()
	{
		app()->setLocale('pl');

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123X',
			'password' => 'password1234#',
			'password_confirmation' => 'password1234#'
		]);

		$res->assertStatus(422)->assertJson([
			'message' => 'Pole hasło musi zawierać jedną dużą i małą literę.'
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123X',
			'password' => 'Password1234',
			'password_confirmation' => 'Password1234'
		]);

		$res->assertStatus(422)->assertJson([
			'message' => 'Pole hasło musi zawierać jeden znak specjalny.'
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123X',
			'password' => 'Passwordoooo#',
			'password_confirmation' => 'Passwordoooo#'
		]);

		$res->assertStatus(422)->assertJson([
			'message' => 'Pole hasło musi zawierać jedną cyfrę.'
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123X',
			'password' => 'Password1234#',
			'password_confirmation' => 'Password1234#'
		]);

		$res->assertStatus(422)->assertJson([
			'message' => 'Podaj aktualne hasło.'
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'password',
			'password_confirmation' => 'password123'
		]);

		$res->assertStatus(422)->assertJson([
			'message' => 'Pole hasło musi mieć przynajmniej 11 znaków.'
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'Password1234#',
			'password_confirmation' => 'Password1234#1'
		]);

		$res->assertStatus(422)->assertJson([
			'message' => 'Potwierdzenie pola hasło nie zgadza się.'
		]);

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'Password1234#',
			'password_confirmation' => 'Password1234#'
		]);

		$res->assertStatus(200)->assertJson([
			'message' => 'Zaktualizowano hasło.'
		]);

		Auth::logout();

		$res = $this->postJson('/web/api/login', [
			'email' => $this->user->email,
			'password' => 'Password1234#'
		]);

		$res->assertStatus(200)->assertJson([
			'message' => 'Zalogowany.'
		]);

		$this->assertNotNull($res['user']);
	}

	/** @test */
	function dont_allow_change_not_logged_user_password()
	{
		Auth::logout();

		app()->setLocale('pl');

		$res = $this->postJson('/web/api/change-password', [
			'password_current' => 'password123',
			'password' => 'password1234',
			'password_confirmation' => 'password1234'
		]);

		$res->assertStatus(401)->assertJson([
			'message' => 'Nie zalogowany.'
		]);
	}

	/** @test */
	function user_logout()
	{
		app()->setLocale('pl');

		$res = $this->getJson('/web/api/logout');

		$res->assertStatus(200)->assertJson([
			'message' => 'Wylogowano.'
		]);
	}
}
