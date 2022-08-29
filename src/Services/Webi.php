<?php

namespace Webi\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Webi
{
	function csrf()
	{
		request()->session()->regenerateToken();

		session(['webi_cnt' => session('webi_cnt') + 1]);

		return response([
			'message' => trans('Csrf token created.'),
			'counter' => session('webi_cnt'),
			'locale' => app()->getLocale(),
			'session_locale' => session('locale'),
		]);
	}

	function locale($locale)
	{
		if (strlen($locale) == 2) {
			app()->setLocale($locale);

			session(['locale' => app()->getLocale()]);

			return response()->json([
				'message' => trans('Locale has been changed.'),
				'locale' => app()->getLocale(),
			], 200);
		}

		throw new Exception('Locale has not been changed.', 422);
	}

	function logout()
	{
		try {
			if (Auth::check()) {
				if (request()->user() instanceof User) {
					request()->user()->update([
						'remember_token' => null
					]);
				}
				Auth::logout();
			}
			request()->session()->flush();
			request()->session()->invalidate();
			request()->session()->regenerateToken();
			session(['locale' => config('app.locale')]);
		} catch (Exception $e) {
			report($e);
			throw new Exception('Logged out error.', 422);
		}

		return response()->json([
			'message' => trans('Logged out.')
		]);
	}
}
