<?php

namespace Webi\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Webi\Events\WebiUserCreated;
use Webi\Exceptions\WebiException;
use Webi\Mail\RegisterMail;
use Webi\Http\Requests\WebiRegisterRequest;

class WebiRegister extends Controller
{
	function index(WebiRegisterRequest $request)
	{
		$valid = $request->validated();
		$user_old = null;
		$user = null;

		$user_old = User::withTrashed()
			->where('email', $valid['email'])
			->first();

		if ($user_old instanceof User) {
			throw new WebiException('An account with this email address exists. Reset your password.');
		}

		try {
			$name = htmlentities(strip_tags($valid['name']), ENT_QUOTES, 'utf-8');

			$user = User::create([
				'name' => $name,
				'email' => $valid['email'],
				'password' => Hash::make($valid['password']),
				'username' => uniqid('user.'),
				'ip' => request()->ip(),
				'code' => uniqid()
			]);
		} catch (Exception $e) {
			report($e);
			throw new WebiException('The account has not been created.');
		}

		try {
			Mail::to($user)
				->locale(app()->getLocale())
				->send(new RegisterMail($user));
		} catch (Exception $e) {
			report($e);
			throw new WebiException('Unable to send activation email, please try to reset your password.');
		}

		// Event
		WebiUserCreated::dispatch($user, request()->ip());

		return response()->success([
			'message' => trans('Account has been created, please confirm your email address.'),
			'created' => true
		], 201);
	}
}
