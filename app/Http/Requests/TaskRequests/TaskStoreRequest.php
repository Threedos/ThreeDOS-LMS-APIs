<?php

namespace App\Http\Requests\TaskRequests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $session = \App\Models\CouncilSession::find($this->council_session_id);
        if (!$session) {
            return false;
        }

        return ((auth()->user()->role->name === 'Head' || auth()->user()->role->name === 'Instructor')
            && auth()->user()->council_id === $session->council_id)
            || auth()->user()->role->name === 'VicePresident';
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
        ];
    }
}
