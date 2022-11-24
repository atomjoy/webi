<?php

namespace Tests\Webi\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class WebiRoutesTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * @test
	 */
	function AllRoutesHaveLocalesMiddleware()
	{
		// Routes
		$routes = app('router')->getRoutes();

		$this->assertNotNull($routes);

		$webi_routes = collect(app('router')->getRoutes())->filter(function (Route $route) {
			if (!preg_match('/^web.api./', $route->getName())) {
				return false;
			}

			return !in_array('webi-locale', $route->gatherMiddleware());
		});

		$this->assertCount(0, $webi_routes);
	}
}
