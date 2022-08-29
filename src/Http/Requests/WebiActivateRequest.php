<?php

namespace Webi\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class WebiActivateRequest extends FormRequest
{
	protected $stopOnFirstFailure = true;

	public function authorize()
	{
		return true; // Allow all
	}

	public function rules()
	{
		return [
			'id' => 'required|numeric|min:1',
			'code' => 'required|string|min:6|max:30',
		];
	}

	public function failedValidation(Validator $validator)
	{
		throw new \Exception($validator->errors()->first(), 422);
	}

	function prepareForValidation()
	{
		$this->merge([
			'id' => request()->route('id'),
			'code' => request()->route('code')
		]);
	}
}
