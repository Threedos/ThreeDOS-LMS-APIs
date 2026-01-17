<?php

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\RolesEnum;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->user()->role->name === RolesEnum::Head->value || $this->user()->role->name === RolesEnum::Instructor->value || $this->user()->role->name === RolesEnum::VicePresident->value) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'council_id' => 'required|exists:councils,id',
            'access_token' => 'nullable|string|max:255',
            'revoked' => 'nullable|boolean',
        ];
    }
}
