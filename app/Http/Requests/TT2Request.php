<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TT2Request extends FormRequest
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
            'inputs'   => ['required', 'array'],
            'inputs.*' => ['required', 'numeric', 'between:0.1,9999.9'],
        ];
    }
}
