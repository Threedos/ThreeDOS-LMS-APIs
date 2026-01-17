<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamMemberRequest extends FormRequest
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
            //
            'members' => 'required|array|min:1',
            'members.*.team_id' => 'required|uuid|exists:teams,id',
            'members.*.user_id' => 'required|uuid|exists:users,id',
            'members.*.rate' => 'nullable|numeric',
            'members.*.role' => 'nullable|string|in:member,leader,co-leader',
            'members.*.task' => 'nullable|string',
        ];
    }
}
