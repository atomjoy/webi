<?php

namespace Webi\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Webi\Exceptions\WebiException;
use Webi\Mail\PasswordMail;
use Webi\Http\Requests\WebiResetPasswordRequest;

class WebiPassReset extends Controller
{
	function index(WebiResetPasswordRequest $request)
	{
		$valid = $request->validated();
		$password = uniqid() . 'zX9#';
		$user = null;

		$user = User::withTrashed()
			->where('email', $valid['email'])
			->first();

		if (
			$user instanceof User
			&& !empty($user->deleted_at)
		) {
			$user->restore(); // Restore if softDeleted
		}

		if (!$user instanceof User) {
			throw new WebiException('Email address does not exists.');
		}

		try {
			if (empty($user->email_verified_at)) {
				$user->email_verified_at = now();
			}
			$user->password = Hash::make($password);
			$user->ip = request()->ip();
			$user->save();
		} catch (Exception $e) {
			report($e);
			throw new WebiException('Password has not been updated.');
		}

		try {
			Mail::to($user)
				->locale(app()->getLocale())
				->send(new PasswordMail($user, $password));
		} catch (Exception $e) {
			report($e);
			throw new WebiException('Unable to send e-mail, please try again later.');
		}

		return response()->success('A new password has been sent to the e-mail address provided.');
	}
}
