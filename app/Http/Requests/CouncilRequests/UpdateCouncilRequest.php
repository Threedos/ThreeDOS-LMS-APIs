<?php

namespace App\Http\Requests\CouncilRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouncilRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if($this->user()->role->name === 'Head' || $this->user()->role->name === 'VicePresident' || $this->user()->role->name === 'President') {
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
            'description' => 'required|string|max:255',
            
        ];
    }
}
