<?php

namespace Webi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Webi\Http\Middleware\WebiAuthRoles;
use Webi\Http\Middleware\WebiCors;
use Webi\Http\Middleware\WebiChangeLocale;
use Webi\Http\Middleware\WebiJsonResponse;
use Webi\Http\Middleware\WebiVerifyCsrfToken;
use Webi\Providers\WebiEventServiceProvider;
use Webi\Services\Webi;
use Webi\Exceptions\WebiHandler;

class WebiServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'webi');

		if (config('webi.settings.error_handler') == true) {
			$this->app->singleton(
				ExceptionHandler::class,
				WebiHandler::class
			);
		}

		$this->app->bind('webi', function ($app) {
			return new Webi();
		});

		$this->app->register(WebiEventServiceProvider::class);
	}

	public function boot(Kernel $kernel)
	{
		// Global
		// $kernel->pushMiddleware(GlobalMiddleware::class);

		// Router
		$this->app['router']->aliasMiddleware('webi-role', WebiAuthRoles::class);
		$this->app['router']->aliasMiddleware('webi-locale', WebiChangeLocale::class);
		$this->app['router']->aliasMiddleware('webi-json', WebiJsonResponse::class);
		$this->app['router']->aliasMiddleware('webi-nocsrf', WebiVerifyCsrfToken::class);
		$this->app['router']->aliasMiddleware('webi-cors', WebiCors::class);

		// Create routes
		if (config('webi.settings.routes') == true) {
			$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
		}

		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'webi');
		$this->loadTranslationsFrom(__DIR__ . '/../lang', 'webi');
		$this->loadJsonTranslationsFrom(__DIR__ . '/../lang');
		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../config/config.php' => config_path('webi.php'),
			], 'webi-config');

			$this->publishes([
				__DIR__ . '/../resources/views' => resource_path('views/vendor/webi')
			], 'webi-email');

			$this->publishes([
				__DIR__ . '/../resources/logo' => public_path('vendor/webi/logo')
			], 'webi-public');

			$this->publishes([
				__DIR__ . '/../lang' => base_path('lang/vendor/webi')
			], 'webi-lang');

			$this->publishes([
				__DIR__ . '/../tests/Webi' => base_path('tests/Webi')
			], 'webi-tests');
		}
	}
}
