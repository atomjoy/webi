<?php

namespace Webi\Facades;

use Illuminate\Support\Facades\Facade;

class Webi extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'webi';
	}
}
