<?php

namespace Webi\Http\Middleware;

use Closure;

/**
 *  Create json response
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure  $next
 * @return mixed
 *
 * Add to Kernel middleware
 * 'web' => [
 * 		\App\Http\Middleware\WebiJsonResponse::class,
 * 		...
 * ]
 *
 * Add to routes
 * Route::fallback(function (){ abort(404, 'API resource not found'); });
 */
class WebiJsonResponse
{
	public function handle($request, Closure $next)
	{
		$request->headers->set('Accept', 'application/json');

		return $next($request);
	}
}
