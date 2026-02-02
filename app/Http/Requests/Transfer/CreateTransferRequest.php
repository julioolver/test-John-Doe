<?php

namespace App\Http\Requests\Transfer;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payer' => 'required|exists:users,id',
            'payee' => 'required|exists:users,id',
            'value' => ['required', 'numeric', 'gt:0', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'payer.required' => 'The payer is required.',
            'payer.exists' => 'The payer does not exist.',
            'payee.required' => 'The payee is required.',
            'payee.exists' => 'The payee does not exist.',
            'value.required' => 'The value is required.',
            'value.numeric' => 'The value must be a number.',
            'value.gt' => 'The value must be greater than 0.',
            'value.regex' => 'The value must have up to 2 decimal places.',
        ];
    }

    public function attributes(): array
    {
        return [
            'payer' => 'Payer',
            'payee' => 'Payee',
            'value' => 'Value',
        ];
    }
}
