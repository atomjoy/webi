<?php

namespace Tests\Webi\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use App\Models\User;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiPassResetTest extends TestCase
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
		$res = $this->postJson('/web/api/reset', [
			'email' => '',
		]);

		$res->assertStatus(422)->assertJson([
			'alert' => [
				'message' => 'The email field is required.',
				'type' => 'danger',
			]
		]);

		$res = $this->postJson('/web/api/reset', [
			'email' => 'error###email',
		]);

		$res->assertStatus(422)->assertJson([
			'alert' => [
				'message' => 'The email must be a valid email address.',
				'type' => 'danger',
			]
		]);
	}

	/** @test */
	function user_reset_password()
	{
		Event::fake([MessageSent::class]);

		$user = User::factory()->create();

		$res = $this->postJson('/web/api/reset', [
			'email' => $user->email
		]);

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'A new password has been sent to the e-mail address provided.',
				'type' => 'success',
			]
		]);

		Event::assertDispatched(MessageSent::class, function ($e) use ($user) {
			$subject = $e->message->getSubject();
			$this->assertMatchesRegularExpression('/Your new password/', $subject);

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
					'message' => 'Authenticated.',
					'type' => 'success',
				]
			]);

			return true;
		});
	}
}
