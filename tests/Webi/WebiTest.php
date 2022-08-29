<?php

namespace Tests\Webi;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class WebiTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * @test
	 */
	function webi_hello()
	{
		$this->assertTrue(true);

		$res = $this->get('/webi/api/scrf')->withCookie('boo', 'scooobedoo');

		$name = $res->headers->getCookies()[0]->getName();

		$this->assertTrue($name == 'boo');

		$val = $res->headers->getCookies()[0]->getValue();

		$this->assertTrue($val == 'scooobedoo');
	}
}
