<?php

namespace Webi\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class WebiChangePasswordRequest extends FormRequest
{
	protected $stopOnFirstFailure = true;

	public function authorize()
	{
		return true; // Allow all
	}

	public function rules()
	{
		return [
			'password_current' => 'required',
			'password' => 'required|min:11|confirmed',
			'password_confirmation' => 'required'
		];
	}

	public function failedValidation(Validator $validator)
	{
		throw new \Exception($validator->errors()->first(), 422);
	}

	function prepareForValidation()
	{
		$this->merge(
			collect(request()->json()->all())->only(['password_current', 'password', 'password_confirmation'])->toArray()
		);
	}
}
