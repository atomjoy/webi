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
			'alert' => [
				'message' => 'Locale has been changed.',
				'type' => 'success'
			],
			'bag' => ['locale' => 'en'],
		]);

		$res = $this->getJson('/web/api/locale/error');
		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Locale has not been changed.',
				'type' => 'danger'
			],
		]);

		$res = $this->getJson('/web/api/csrf');
		$res->assertStatus(200);

		$res = $this->getJson('/web/api/csrf');
		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Csrf token created.',
				'type' => 'success'
			],
			'bag' => ['locale' => 'en', 'counter' => 2],
		]);
	}
}
