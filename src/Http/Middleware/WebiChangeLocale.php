<?php

namespace Webi\Http\Middleware;

use Closure;

/**
 * Load language locale from session
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure  $next
 * @return mixed
 *
 * Add in app/Http/Kernel.php
 *
 * protected $routeMiddleware = [
 * 		'webi-locale' => \App\Http\Middleware\WebiChangeLocale::class,
 * ]
 *
 * * then
 * Route::middleware(['web', 'webi-locale']);
 */
class WebiChangeLocale
{
	public function handle($request, Closure $next)
	{
		$lang =  session('locale', config('app.locale'));

		app()->setLocale($lang);

		if ($request->has('locale')) {
			app()->setLocale($request->query('locale'));
		}

		return $next($request);
	}
}
