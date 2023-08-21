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
             'first_name' => 'sometimes|required|string',
             'last_name' => 'sometimes|required|string',
             'email' => 'sometimes|required|email:unique:users',
             'password' => 'sometimes|required|confirmed',
             'address' => 'sometimes|required|string',
             'phone_number' => 'sometimes|required',
        ];
    }
}
