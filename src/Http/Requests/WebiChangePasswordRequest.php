<?php

namespace Webi\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Webi\Traits\HasStripTags;

class WebiChangePasswordRequest extends FormRequest
{
	use HasStripTags;

	protected $stopOnFirstFailure = true;

	public function authorize()
	{
		if (auth()->user() instanceof User) {
			return true; // Allow all
		}
		return false;
	}

	public function rules()
	{
		return [
			'password_current' => 'required',
			'password' => [
				'required',
				Password::min(11)->letters()->mixedCase()->numbers()->symbols(),
				'confirmed',
				'max:50',
			],
			'password_confirmation' => 'required',
		];
	}

	public function failedValidation(Validator $validator)
	{
		throw new ValidationException($validator, response()->json([
			'message' => $validator->errors()->first()
		], 422));
	}

	function prepareForValidation()
	{
		$this->merge(
			$this->stripTags(collect(request()->json()->all())->only(['password_current', 'password', 'password_confirmation'])->toArray())
		);
	}
}
