<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaginatedRequest extends FormRequest
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
            'pageSize' => 'nullable|integer',
            'search' => 'nullable|string',
            'sort' => 'nullable|string',
            'role' => 'nullable|string',

        ];
    }
}
