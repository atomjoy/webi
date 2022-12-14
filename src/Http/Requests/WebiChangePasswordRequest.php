<?php

namespace Webi\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Webi\Exceptions\WebiException;
use Webi\Traits\HasStripTags;

class WebiChangePasswordRequest extends FormRequest
{
	use HasStripTags;
	
	protected $stopOnFirstFailure = true;

	public function authorize()
	{
		return true; // Allow all
	}

	public function rules()
	{
		return [
			'password_current' => 'required',
			'password' => [
				'required',
				Password::min(11)->letters()->mixedCase()->numbers()->symbols(),
				'confirmed',
			],
			'password_confirmation' => 'required',
		];
	}

	public function failedValidation(Validator $validator)
	{
		throw new WebiException($validator->errors()->first());
	}

	function prepareForValidation()
	{
		$this->merge(
			$this->stripTags(collect(request()->json()->all())->only(['password_current', 'password', 'password_confirmation'])->toArray())
		);
	}
}
