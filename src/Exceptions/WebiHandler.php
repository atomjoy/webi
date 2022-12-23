<?php

namespace Webi\Exceptions;

use Error;
use Exception;
use Throwable;
use PDOException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webi\Exceptions\WebiException;

class WebiHandler extends ExceptionHandler
{
	/**
	 * A list of exception types with their corresponding custom log levels.
	 *
	 * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
	 */
	protected $levels = [
		//
	];

	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array<int, class-string<\Throwable>>
	 */
	protected $dontReport = [
		//
	];

	/**
	 * A list of the inputs that are never flashed to the session on validation exceptions.
	 *
	 * @var array<int, string>
	 */
	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
		'remember_token',
		'code',
	];

	/**
	 * Register the exception handling callbacks for the application.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->renderable(function (Error $e, $request) {
			if ($request->is('web/api/*') || $request->wantsJson()) {
				return response()->errors($e->getMessage() ?? 'Unknown Error.', null, 422);
			}
		});

		$this->renderable(function (PDOException $e, $request) {
			if ($request->is('web/api/*') || $request->wantsJson()) {
				return response()->errors('Database error.', null, 500);
			}
		});

		$this->renderable(function (NotFoundHttpException $e, $request) {
			if ($request->is('web/api/*') || $request->wantsJson()) {
				return response()->errors('Not Found.', null, 404);
			}
		});

		$this->renderable(function (AuthenticationException $e, $request) {
			if ($request->is('web/api/*') || $request->wantsJson()) {
				return response()->errors($e->getMessage(), null, 401);
			}
		});

		$this->renderable(function (ValidationException $e, $request) {
			if ($request->is('web/api/*') || $request->wantsJson()) {
				return response()->errors($e->getMessage(), null, 422);
			}
		});

		$this->renderable(function (WebiException $e, $request) {
			if ($request->is('web/api/*') || $request->wantsJson()) {
				return response()->errors($e->getMessage(), null, 422);
			}
		});

		$this->renderable(function (Exception $e, $request) {
			if ($request->is('web/api/*') || $request->wantsJson()) {
				return response()->errors($e->getMessage() ?? 'Unknown Exception.', null, $this->validCode($e));
			}
		});
	}

	/**
	 * Refresh session locale.
	 *
	 * @return void
	 */
	public function updateLocale()
	{
		$lang =  session('locale', config('app.locale'));
		app()->setLocale($lang);
		if (request()->has('locale')) {
			app()->setLocale(request()->query('locale'));
		}
	}

	/**
	 * Http codes validation.
	 *
	 * @return bool
	 */
	public function validCode($e)
	{
		return ($e->getCode() >= 100 && $e->getCode() <= 599) ? $e->getCode() : 422;
	}
}
