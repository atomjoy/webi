<?php

namespace Webi\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Webi\Exceptions\WebiException;
use Webi\Traits\HasStripTags;

class WebiLoginRequest extends FormRequest
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
			'email' => ['required', $email, 'max:191'],
			'password' => 'required|min:11',
			'remember_me' => 'sometimes|boolean'
		];
	}

	public function failedValidation(Validator $validator)
	{
		throw new WebiException($validator->errors()->first());
	}

	function prepareForValidation()
	{
		$this->merge(
			$this->stripTags(collect(request()->json()->all())->only(['email', 'password', 'remember_me'])->toArray())
		);
	}
}
