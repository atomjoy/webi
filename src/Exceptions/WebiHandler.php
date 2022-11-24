<?php

namespace Webi\Exceptions;

use Throwable;
use PDOException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
		$this->reportable(function (Throwable $e) {
			//
		});

		$this->renderable(function (Throwable $e, $request) {
			// Change "ServerError" Exception when app_debug=false to a json message
			if (
				$request->wantsJson() ||
				$request->is('api/*') ||
				$request->is('web/*')
			) {
				$message = $e->getMessage();

				if ($e instanceof QueryException || $e instanceof PDOException) {
					$message = 'Database error.';
				}

				$message = empty($message) ? 'Unknown Exception.' : $message;

				$status = ($e->getCode() >= 100 && $e->getCode() <= 599) ? $e->getCode() : 422;

				if ($e instanceof AuthenticationException) {
					$status = 401;
				}

				if ($e instanceof NotFoundHttpException) {
					$message = 'Not Found.';
				}

				if (config('webi.settings.translate_response') == true) {
					$message = trans($message);
				}

				$data['message'] = $message;
				$data['code'] = $status;
				$data['data'] = null;

				if (config('app.debug')) {
					$data['error'] = [
						'exception' => get_class($e),
						'file' => $e->getFile(),
						'line' => $e->getLine(),
						'trace' => collect($e->getTrace())->map(fn ($trace) => Arr::except($trace, ['args']))->all(),
					];
				}

				return response()->json($data, $status, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			}
		});
	}
}
