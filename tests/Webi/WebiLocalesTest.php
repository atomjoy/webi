<?php

namespace Tests\Webi;

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
			'message' => 'Zmieniono język.',
			'locale' => 'pl'
		]);

		$res = $this->getJson('/web/api/locale/error');

		$res->assertStatus(422)->assertJson([
			'message' => 'Nie zmieniono języka.',
		]);
	}

	/** @test */
	function change_locale_en()
	{
		$res = $this->getJson('/web/api/locale/en');

		$res->assertStatus(200)->assertJson([
			'message' => 'Locale has been changed.',
			'locale' => 'en'
		]);

		$res = $this->getJson('/web/api/locale/error');

		$res->assertStatus(422)->assertJson([
			'message' => 'Locale has not been changed.',
		]);
	}

	/** @test */
	function change_locales()
	{
		$res = $this->getJson('/web/api/locale/pl');

		$res->assertStatus(200)->assertJson([
			'message' => 'Zmieniono język.',
			'locale' => 'pl'
		]);

		$res = $this->getJson('/web/api/csrf');

		$res->assertStatus(200)->assertJson([
			'message' => 'Utworzono csrf token.',
			'locale' => 'pl'
		]);

		$res = $this->getJson('/web/api/locale/en');

		$res->assertStatus(200)->assertJson([
			'message' => 'Locale has been changed.',
			'locale' => 'en'
		]);

		$res = $this->getJson('/web/api/csrf');

		$res->assertStatus(200)->assertJson([
			'message' => 'Csrf token created.',
			'locale' => 'en',
			'counter' => 2,
		]);

		$res = $this->getJson('/web/api/csrf?locale=pl');

		$res->assertStatus(200)->assertJson([
			'message' => 'Utworzono csrf token.',
			'locale' => 'pl',
			'counter' => 3
		]);

		$res = $this->getJson('/web/api/csrf?locale=en');

		$res->assertStatus(200)->assertJson([
			'message' => 'Csrf token created.',
			'locale' => 'en',
			'counter' => 4
		]);
	}
}
