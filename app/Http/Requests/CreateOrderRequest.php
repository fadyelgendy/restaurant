<?php

namespace App\Http\Requests;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateOrderRequest extends FormRequest
{
    use ResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->role == \App\Enums\Role::CUSTOMER->value;
    }

    protected function failedAuthorization()
    {
        if ($this->is("api/*")) {
            throw new HttpResponseException($this->failResponseJson(trans('Unauthorized Access!'), 403));
        }

        return parent::failedAuthorization();
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->is("api/*")) {
            throw new HttpResponseException($this->failResponseJson($validator->errors()->messages(), 422));
        }

        return parent::failedValidation($validator);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'numeric', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'gt:0']
        ];
    }
}
