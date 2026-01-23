<?php

namespace App\Http\Requests\AttendanceRequests;

use Illuminate\Foundation\Http\FormRequest;
    use App\Models\CouncilSession;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

public function authorize(): bool
{
    $user = auth()->user();

    if ($user->role->name === 'VicePresident') {
        return true;
    }

    if (!in_array($user->role->name, ['Head', 'Instructor'], true)) {
        return false;
    }

    $session = CouncilSession::find($this->council_session_id);

    return $session && $session->council_id === $user->council_id;
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
            'user_id' => 'required|exists:users,id',
            'council_session_id' => 'required|exists:council_sessions,id',
            'status' => 'required|in:present,absent,late',

        ];
    }
}
