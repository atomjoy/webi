<?php

namespace Tests\Webi\Api;

use Tests\TestCase;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiChangeLocalesTest extends TestCase
{
	/** @test */
	function change_locale_en()
	{
		$res = $this->getJson('/web/api/locale/en');
		$res->assertStatus(200)->assertJson([
			'message' => 'Locale has been changed.',
			'data' => ['locale' => 'en'],
		]);

		$res = $this->getJson('/web/api/locale/error');
		$res->assertStatus(200)->assertJson([
			'message' => 'Locale has not been changed.',
		]);

		$res = $this->getJson('/web/api/csrf');
		$res->assertStatus(200);

		$res = $this->getJson('/web/api/csrf');
		$res->assertStatus(200)->assertJson([
			'message' => 'Csrf token created.',
			'data' => ['locale' => 'en', 'session_locale' => 'en', 'counter' => 2],
		]);
	}
}
