<?php

namespace Webi\Traits;

trait HasStripTags
{
	function stripTags($arr)
	{
		array_walk_recursive($arr, function (&$v) {
			$v = trim(strip_tags($v));
		});

		return $arr;
	}

	function htmlentities($arr)
	{
		array_walk_recursive($arr, function (&$v) {
			$v = htmlentities($v, ENT_QUOTES, "UTF-8");
		});

		return $arr;
	}
}
