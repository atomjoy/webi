<?php

namespace Tests\Webi\Api;

use Tests\TestCase;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiLocalesTest extends TestCase
{
	/** @test */
	function change_locale_pl()
	{
		$res = $this->getJson('/web/api/locale/pl');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Zmieniono język.',
				'type' => 'success',
			],
			'bag' => ['locale' => 'pl'],
		]);

		$res = $this->getJson('/web/api/locale/error');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Nie zmieniono języka.',
				'type' => 'danger',
			],
		]);
	}

	/** @test */
	function change_locale_en()
	{
		$res = $this->getJson('/web/api/locale/en');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Locale has been changed.',
				'type' => 'success',
			],
			'bag' => ['locale' => 'en'],
		]);

		$res = $this->getJson('/web/api/locale/error');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Locale has not been changed.',
				'type' => 'danger',
			],
		]);
	}

	/** @test */
	function change_locales()
	{
		$res = $this->getJson('/web/api/locale/pl');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Zmieniono język.',
				'type' => 'success',
			],
			'bag' => ['locale' => 'pl'],
		]);

		$res = $this->getJson('/web/api/csrf');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Utworzono csrf token.',
				'type' => 'success',
			],
			'bag' => ['locale' => 'pl'],
		]);

		$res = $this->getJson('/web/api/locale/en');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Locale has been changed.',
				'type' => 'success',
			],
			'bag' => ['locale' => 'en'],
		]);

		$res = $this->getJson('/web/api/csrf');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Csrf token created.',
				'type' => 'success',
			],
			'bag' => ['locale' => 'en', 'counter' => 2],
		]);

		$res = $this->getJson('/web/api/csrf?locale=pl');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Utworzono csrf token.',
				'type' => 'success',
			],
			'bag' => ['locale' => 'pl', 'counter' => 3],
		]);

		$res = $this->getJson('/web/api/csrf?locale=en');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Csrf token created.',
				'type' => 'success',
			],
			'bag' => ['locale' => 'en', 'counter' => 4],
		]);
	}
}
