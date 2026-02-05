<?php

namespace App\Http\Requests\TaskSubmissionRequests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskSubmissionRequest extends FormRequest
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
            'task_id' => 'required|exists:tasks,id',
            'file' => 'required|string',
            'user_id' => 'sometimes|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'task_id.required' => 'Task ID is required',
            'task_id.exists' => 'Task ID does not exist',
            'file.required' => 'File is required',
            'user_id.exists' => 'User ID does not exist',
        ];
    }
}
