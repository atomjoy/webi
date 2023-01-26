<?php

namespace Webi\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Webi\Exceptions\WebiException;
use Webi\Traits\HasStripTags;

class WebiActivateRequest extends FormRequest
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
			'id' => 'required|numeric|min:1',
			'code' => 'required|string|min:6|max:30',
		];
	}

	public function failedValidation(Validator $validator)
	{
		throw new WebiException($validator->errors()->first());
	}

	function prepareForValidation()
	{
		$arr = [
			'id' => request()->route('id'),
			'code' => request()->route('code')
		];

		$this->merge(
			$this->stripTags($arr)
		);
	}
}
