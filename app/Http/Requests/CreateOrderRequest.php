<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //TODO: Logged in Users

        return true;
    }

    protected function failedAuthorization()
    {
        if ($this->is("api/*")) {
            throw new HttpResponseException(response()->json([
                'status' => 403,
                'errors' => ['error' => 'Unauthorized Access']
            ]));
        }

        return parent::failedAuthorization();
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->is("api/*")) {
            throw new HttpResponseException(response()->json([
                'status' => 422,
                'errors' => $validator->errors()->messages()
            ]));
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
