<?php

namespace App\Http\Requests\SessionRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSessionRequest extends FormRequest
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
            'title' => 'sometimes|string',
            'date' => 'sometimes|date',//sometimes for optional fields
            'description' => 'nullable|string',
            'material' => 'nullable|string',
            'council_id' => 'sometimes|exists:councils,id',//sometimes for optional fields

        ];
    }
}
