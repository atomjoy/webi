<?php

namespace Webi\Traits\Logs;

use Webi\Enums\Log\LogType;
use Illuminate\Support\Facades\Log;

trait WebiLog
{
	static function info($event, LogType $type = LogType::CREATED)
	{
		$day = date('Y-m-d', time());
		$time = date('Y-m-d H:i:s', time());

		Log::build([
			'driver' => 'single',
			'path' => storage_path('logs/webi-' . $day . '.log'),
		])->info("EVENT: " . strtoupper($type->value) . " UID:" . $event->user->id . " IP:" . $event->ip_address . " DATE:" . $time);
	}
}
