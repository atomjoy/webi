<?php

namespace Webi\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Webi\Traits\Http\HasJsonResponse;

class Webi
{
	use HasJsonResponse;

	function csrf()
	{
		request()->session()->regenerateToken();
		session(['webi_cnt' => session('webi_cnt') + 1]);

		return response()->success([
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

			return response()->success([
				'message' => trans('Locale has been changed.'),
				'locale' => app()->getLocale(),
			]);
		}

		return response()->errors('Locale has not been changed.');
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
			return response()->errors('Logged out error.');
		}

		return response()->success('Logged out.');
	}
}
