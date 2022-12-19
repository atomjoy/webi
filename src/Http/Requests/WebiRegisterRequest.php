<?php

namespace Webi\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Webi\Exceptions\WebiException;
use Webi\Traits\HasStripTags;

class WebiRegisterRequest extends FormRequest
{
	use HasStripTags;
	
	protected $stopOnFirstFailure = true;

	public function authorize()
	{
		return true; // Allow all
	}

	public function rules()
	{
		$email = 'email:rfc,dns';
		if (env('APP_DEBUG') == true) {
			$email = 'email';
		}

		return [
			'name' => 'required|min:3|max:50',
			'email' => [
				'required', $email, 'max:191',
				Rule::unique('users')->whereNull('deleted_at')
			],
			'password' => [
				'required',
				Password::min(11)->letters()->mixedCase()->numbers()->symbols(),
				'confirmed',
			],
			'password_confirmation' => 'required'
		];
	}

	public function failedValidation(Validator $validator)
	{
		throw new WebiException($validator->errors()->first());
	}

	function prepareForValidation()
	{
		$this->merge(
			$this->stripTags(collect(request()->json()->all())->only(['name', 'email', 'password', 'password_confirmation'])->toArray())
		);
	}
}
