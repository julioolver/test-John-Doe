<?php

namespace App\Http\Requests\User;

use App\Domain\User\ValueObjects\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'document' => 'required|string|max:255|unique:users,document',
            'document_type' => ['required', new Enum(DocumentType::class)],
            'password' => 'required|string|min:6',
        ];
    }
}
