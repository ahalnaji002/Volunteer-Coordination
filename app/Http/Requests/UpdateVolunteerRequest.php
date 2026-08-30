<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVolunteerRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $volunteer = $this->route('volunteer');

        return [
            'name' => 'sometimes|required|string|max:255',

            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users', 'email')
                    ->ignore($volunteer->user_id),
            ],

            'phone' => 'sometimes|required|numeric|digits:10',

            'national_id' => [
                'sometimes',
                'required',
                'numeric',
                'digits:9',
                Rule::unique('volunteers', 'national_id')
                    ->ignore($volunteer->id),
            ],
        ];
    }
}
