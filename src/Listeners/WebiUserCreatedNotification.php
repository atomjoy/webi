<?php

namespace Webi\Listeners;

use Webi\Events\WebiUserCreated;
use Webi\Enums\Log\LogType;
use Webi\Traits\Logs\WebiLog;

class WebiUserCreatedNotification
{
	public function handle(WebiUserCreated $event)
	{
		if (config('webi.event.log_created') == true) {
			WebiLog::info($event, LogType::CREATED);
		}
	}
}
