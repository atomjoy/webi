<?php

namespace Webi\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
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
			// Json response
			if (
				$request->is('web/*') ||
				$request->is('api/*') ||
				$request->wantsJson()
			) {
				$msg = empty($e->getMessage()) ? 'Not Found' : $e->getMessage();
				$code = empty($e->getCode()) ? 404 : $e->getCode();

				if ($e instanceof AuthenticationException) {
					$code = 401;
					$msg = $msg;
				}

				if ($e instanceof NotFoundHttpException) {
					$msg = 'Not Found';
				}

				if (strlen($code) < 3 || strlen($code) > 3) {
					$code = 422;
				}

				if (config('app.debug')) {
					return response()->json([
						'message' => trans($msg), // Translate message
						'code' => $code,
						'ex' => [
							'name' => $this->getClassName(get_class($e)),
							'namespace' => get_class($e),
							'message' => $e->getMessage(),
							'code' => $e->getCode(),
						],
					], $code);
				}

				return response()->json([
					'message' => $msg,
					'code' => $code,
				], $code);
			}
		});
	}

	/**
	 * Get exception class name without namespace.
	 *
	 * @return string
	 */
	static function getClassName($e)
	{
		$path = explode('\\', $e);
		return array_pop($path);
	}
}
