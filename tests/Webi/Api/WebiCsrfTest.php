<?php

namespace Tests\Webi\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiCsrfTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function csrf_session_counter()
	{
		$res = $this->get('/web/api/csrf');

		$res->assertStatus(200)->assertJson([
			'message' => 'Csrf token created.',
			'counter' => 1
		]);

		$token = [
			$res->headers->getCookies()[0]->getName() => $res->headers->getCookies()[0]->getValue(),
			$res->headers->getCookies()[1]->getName() => $res->headers->getCookies()[1]->getValue(),
		];

		$res = $this->withCookies($token)->get('/web/api/csrf');

		$res->assertStatus(200)->assertJson([
			'message' => 'Csrf token created.',
			'counter' => 2
		]);

		// $cookies = $res->headers->getCookies();
	}
}
