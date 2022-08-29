<?php

namespace Webi\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Webi\Listeners\WebiUserCreatedNotification;
use Webi\Listeners\WebiUserLoggedNotification;
use Webi\Events\WebiUserCreated;
use Webi\Events\WebiUserLogged;

class WebiEventServiceProvider extends ServiceProvider
{
	protected $listen = [
		WebiUserLogged::class => [
			WebiUserLoggedNotification::class,
		],
		WebiUserCreated::class => [
			WebiUserCreatedNotification::class,
		]
	];

	/**
	 * Register any events for your application.
	 *
	 * @return void
	 */
	public function boot()
	{
		parent::boot();
	}
}
