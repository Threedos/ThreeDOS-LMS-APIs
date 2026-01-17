<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamMemberRequest extends FormRequest
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
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'required | exists:users,id',
            'rate' => 'nullable | numeric | between:0,10',
            'role' => 'required | in:Leader,Member,Co-Leader',
            'task' => 'nullable | string'
        ];
    }
}
