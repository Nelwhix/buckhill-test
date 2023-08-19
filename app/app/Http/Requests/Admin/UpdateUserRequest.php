<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
     * @return non-empty-array<string, string>
     */
    public function rules(): array
    {
        return [
             'first_name' => 'required|string',
             'last_name' => 'required|string',
             'email' => 'required|email:unique:users',
             'password' => 'required|confirmed',
             'address' => 'required|string',
             'phone_number' => 'required',
        ];
    }
}
