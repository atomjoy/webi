<?php

namespace Tests\Webi\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/*
	php artisan vendor:publish --tag=webi-tests --force
	php artisan test --testsuite=Webi --stop-on-failure
*/

class WebiFacadeTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		parent::setUp();

		Route::get('/web/api/csrf/facade', function () {
			// Vscode show error but it is facade !
			// return Webi::csrf();

			// With namespace
			return \Webi\Facades\Webi::csrf();
		})->name('facade.csrf')->middleware(['web', 'webi-locale']);
	}

	/** @test */
	function csrf_facade()
	{
		$res = $this->get('/web/api/csrf/facade');

		$res->assertStatus(200)->assertJson([
			'alert' => [
				'message' => 'Csrf token created.',
				'type' => 'success'
			],
		]);
	}
}
