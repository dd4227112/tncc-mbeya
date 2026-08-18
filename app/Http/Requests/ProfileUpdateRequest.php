<?php

namespace App\Http\Requests;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => [
                'string',
                'size:13',
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (! isValidPhone($value)) {
                        $fail('The :attribute must be a valid Tanzanian phone number.');
                    }
                },
            ],
            'address' => ['string', 'max:50', 'nullable'],
        ];
    }
}
