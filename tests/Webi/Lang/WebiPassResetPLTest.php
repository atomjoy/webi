<?php

namespace Tests\Webi\Lang;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use App\Models\User;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiPassResetPLTest extends TestCase
{
	use RefreshDatabase;

	function getPassword($html)
	{
		preg_match('/word>[a-zA-Z0-9#]+<\/pass/', $html, $matches, PREG_OFFSET_CAPTURE);
		return str_replace(['word>', '</pass'], '', end($matches)[0]);
	}

	/** @test */
	function reset_password_errors()
	{
		app()->setLocale('pl');

		$res = $this->postJson('/web/api/reset', [
			'email' => '',
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Pole adres email jest wymagane.',
				'type' => 'danger',
			],
		]);

		$res = $this->postJson('/web/api/reset', [
			'email' => 'error###email',
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Pole adres email nie jest poprawnym adresem e-mail.',
				'type' => 'danger',
			],
		]);
	}

	/** @test */
	function user_reset_password()
	{
		app()->setLocale('pl');

		Event::fake([MessageSent::class]);

		$user = User::factory()->create();

		$res = $this->postJson('/web/api/reset', [
			'email' => $user->email
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Wysłano hasło na podany adres email.',
				'type' => 'success',
			],
		]);

		Event::assertDispatched(MessageSent::class, function ($e) use ($user) {
			$subject = $e->message->getSubject();
			$this->assertMatchesRegularExpression('/Twoje nowe hasło/', $subject);

			// $html = $e->message->getBody();
			$html = $e->message->getHtmlBody();
			$this->assertMatchesRegularExpression('/word>[a-zA-Z0-9#]+<\/pass/', $html);
			$pass = $this->getPassword($html);

			$res = $this->postJson('/web/api/login', [
				'email' => $user->email,
				'password' => $pass,
			]);

			$res->assertStatus(200)->assertJson([
				'alert' => [
					'message' => 'Zalogowany.',
					'type' => 'success',
				],
			]);

			return true;
		});
	}
}
