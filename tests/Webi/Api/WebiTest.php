<?php

namespace Tests\Webi\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class WebiTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * @test
	 */
	function example()
	{
		$this->assertTrue(true);

		$res = $this->get('/webi/api/scrf')->withCookie('boo', 'scooobeeedooo');

		$name = '';
		$value = '';

		$name = collect($res->headers->getCookies())->map(function ($cookie) {
			return $cookie->getName();
		})->all();

		$this->assertTrue(in_array('boo', $name));

		$value = collect($res->headers->getCookies())->map(function ($cookie) {
			return $cookie->getValue();
		})->all();

		$this->assertTrue(in_array('scooobeeedooo', $value));
	}
}
