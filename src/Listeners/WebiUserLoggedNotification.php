<?php

namespace Webi\Listeners;

use Webi\Events\WebiUserLogged;
use Webi\Enums\Log\LogType;
use Webi\Traits\Logs\WebiLog;

class WebiUserLoggedNotification
{
	public function handle(WebiUserLogged $event)
	{
		if (config('webi.event.log_logged') == true) {
			WebiLog::info($event, LogType::LOGGED);
		}
	}
}
