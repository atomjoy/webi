<?php

namespace Webi\Http\Controllers;

use Exception;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webi\Traits\Http\HasJsonResponse;

class WebiLogout extends Controller
{
	use HasJsonResponse;

	function index(Request $request)
	{
		try {
			// If Logged
			if (Auth::check()) {
				// Delete remember me token
				if ($request->user() instanceof User) {
					$request->user()->update([
						'remember_token' => null
					]);
				}
				// Logout
				Auth::logout();
			}

			// Delete session
			$request->session()->flush();
			$request->session()->invalidate();
			$request->session()->regenerateToken();
			session(['locale' => config('app.locale')]);
		} catch (Exception $e) {
			report($e);
			throw new Exception('Logged out error.', 422);
		}

		return $this->jsonResponse('Logged out.');
	}
}
