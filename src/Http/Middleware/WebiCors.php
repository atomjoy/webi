<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Cros domain
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure  $next
 * @return mixed
 *
 * Add in app/Http/Kernel.php
 *
 * protected $routeMiddleware = [
 * 		'webi-cors' => \App\Http\Middleware\WebiCors::class,
 * ]
 *
 * * then
 * Route::middleware(['webi-cors']);
 */
class Cors
{
	public function handle($request, Closure $next)
	{
		// CORS allow domain and all sub-domains
		return $next($request)
			->header('Access-Control-Allow-Origin', '.' . $request->getHttpHost())
			->header('Access-Control-Allow-Credentials', true)
			->header('Access-Control-Max-Age', 86400)
			->header('Access-Control-Allow-Methods', 'HEAD, OPTIONS, GET, POST, PUT, PATCH, DELETE')
			->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Accept-Encoding, Accept-Language, Cookie, Set-Cookie, Cookie2, Set-Cookie2, Authorization, X-Authorization, X-Custom-Header, X-Token, X-Requested-With, X-Csrf-Token, X-Xsrf-Token, Cache-Control, Pragma, Upgrade-Insecure-Requests');
	}
}

/*
	// ->header('Access-Control-Allow-Methods', '*')
	// ->header('Access-Control-Allow-Methods', 'HEAD, OPTIONS, GET, PUT, PATCH, POST, DELETE')
	// ->header('Access-Control-Allow-Methods', 'CONNECT, DEBUG, DELETE, DONE, GET, HEAD, HTTP, HTTP/0.9, HTTP/1.0, HTTP/1.1, HTTP/2, OPTIONS, ORIGIN, ORIGINS, PATCH, POST, PUT, QUIC, REST, SESSION, SHOULD, SPDY, TRACE, TRACK')
	// ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Accept-Language, Cookie, Set-Cookie, Cookie2, Set-Cookie2, Cache-Control, Pragma, X-Requested-With, X-Csrf-Token, X-Xsrf-Token, Authorization, X-Authorization, X-Custom-Header, Content-Disposition, Content-Length, Content-Language, X-Forwarded-For, X-Forwarded-Host, X-Forwarded-Proto, Upgrade-Insecure-Requests')
	// ->header('Access-Control-Allow-Headers', 'Accept, Accept-CH, Accept-Charset, Accept-Datetime, Accept-Encoding, Accept-Ext, Accept-Features, Accept-Language, Accept-Params, Accept-Ranges, Access-Control-Allow-Credentials, Access-Control-Allow-Headers, Access-Control-Allow-Methods, Access-Control-Allow-Origin, Access-Control-Expose-Headers, Access-Control-Max-Age, Access-Control-Request-Headers, Access-Control-Request-Method, Age, Allow, Alternates, Authentication-Info, Authorization, C-Ext, C-Man, C-Opt, C-PEP, C-PEP-Info, CONNECT, Cache-Control, Compliance, Connection, Content-Base, Content-Disposition, Content-Encoding, Content-ID, Content-Language, Content-Length, Content-Location, Content-MD5, Content-Range, Content-Script-Type, Content-Security-Policy, Content-Style-Type, Content-Transfer-Encoding, Content-Type, Content-Version, Cookie, Cost, DAV, DELETE, DNT, DPR, Date, Default-Style, Delta-Base, Depth, Derived-From, Destination, Differential-ID, Digest, ETag, Expect, Expires, Ext, From, GET, GetProfile, HEAD, HTTP-date, Host, IM, If, If-Match, If-Modified-Since, If-None-Match, If-Range, If-Unmodified-Since, Keep-Alive, Label, Last-Event-ID, Last-Modified, Link, Location, Lock-Token, MIME-Version, Man, Max-Forwards, Media-Range, Message-ID, Meter, Negotiate, Non-Compliance, OPTION, OPTIONS, OWS, Opt, Optional, Ordering-Type, Origin, Overwrite, P3P, PEP, PICS-Label, POST, PUT, Pep-Info, Permanent, Position, Pragma, ProfileObject, Protocol, Protocol-Query, Protocol-Request, Proxy-Authenticate, Proxy-Authentication-Info, Proxy-Authorization, Proxy-Features, Proxy-Instruction, Public, RWS, Range, Referer, Refresh, Resolution-Hint, Resolver-Location, Retry-After, Safe, Sec-Websocket-Extensions, Sec-Websocket-Key, Sec-Websocket-Origin, Sec-Websocket-Protocol, Sec-Websocket-Version, Security-Scheme, Server, Set-Cookie, Set-Cookie2, SetProfile, SoapAction, Status, Status-URI, Strict-Transport-Security, SubOK, Subst, Surrogate-Capability, Surrogate-Control, TCN, TE, TRACE, Timeout, Title, Trailer, Transfer-Encoding, UA-Color, UA-Media, UA-Pixels, UA-Resolution, UA-Windowpixels, URI, Upgrade, User-Agent, Variant-Vary, Vary, Version, Via, Viewport-Width, WWW-Authenticate, Want-Digest, Warning, Width, X-Content-Duration, X-Content-Security-Policy, X-Content-Type-Options, X-CustomHeader, X-DNSPrefetch-Control, X-Forwarded-For, X-Forwarded-Port, X-Forwarded-Proto, X-Frame-Options, X-Modified, X-OTHER, X-PING, X-PINGOTHER, X-Powered-By, X-Requested-With')
	// Allow headers from js
	// ->header('Access-Control-Expose-Headers', '*, Authorization')

	// Set csrf token html
    <meta name="csrf-token" content="{{ csrf_token() }}">

    // Get csrf token js
    const csrf = document.querySelector('meta[name="csrf-token"]').attr('content')

    // Kernel.php
    protected $routeMiddleware = [
        'cors' => \App\Http\Middleware\Cors::class,
    ];

    // Route
    Route::middleware(['cors'])->group(['domain' => '{username}.app.xx'], function() {
        Route::get('/cors/user', ['UserController', 'index']);
        Route::post('/cors/domain', function($username) {
          // ...
        });
    });
*/