<?php

namespace App\Http\Requests\TaskSubmissionRequests;

use Illuminate\Foundation\Http\FormRequest;

class TaskSubmissionPaginatedRequest extends FormRequest
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
            'pageIndex' => 'required|integer',
            'pageSize' => 'required|integer',
            'search' => 'string|nullable',
            'filter' => 'string|nullable',
            'sort' => 'string|nullable',
        ];
    }
}
