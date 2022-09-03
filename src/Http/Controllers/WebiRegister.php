<?php

namespace Webi\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Webi\Events\WebiUserCreated;
use Webi\Mail\RegisterMail;
use Webi\Http\Requests\WebiRegisterRequest;
use Webi\Traits\Http\HasJsonResponse;

class WebiRegister extends Controller
{
	use HasJsonResponse;

	function index(WebiRegisterRequest $request)
	{
		$valid = $request->validated();
		$user_old = null;
		$user = null;

		try {
			$user_old = User::withTrashed()
				->where('email', $valid['email'])
				->first();
		} catch (Exception $e) {
			report($e);
			throw new Exception('Database error.', 422);
		}

		if ($user_old instanceof User) {
			throw new Exception('An account with this email address exists. Reset your password.', 422);
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
			throw new Exception('The account has not been created.', 422);
		}

		try {
			Mail::to($user)
				->locale(app()->getLocale())
				->send(new RegisterMail($user));
		} catch (Exception $e) {
			report($e);
			throw new Exception('Unable to send activation email, please try to reset your password.', 422);
		}

		// Event
		WebiUserCreated::dispatch($user, request()->ip());

		return $this->jsonResponse('Account has been created, please confirm your email address.', [
			'created' => true
		], 201);
	}
}
