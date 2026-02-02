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
            'amount' => 'required|numeric|min:0',
            'created_at' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'payer.required' => 'The payer is required.',
            'payer.exists' => 'The payer does not exist.',
            'payee.required' => 'The payee is required.',
            'payee.exists' => 'The payee does not exist.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be greater than 0.',
            'created_at.required' => 'The created at is required.',
            'created_at.date' => 'The created at must be a date.',
        ];
    }

    public function attributes(): array
    {
        return [
            'payer' => 'Payer',
            'payee' => 'Payee',
            'amount' => 'Amount',
            'created_at' => 'Created at',
        ];
    }
}
