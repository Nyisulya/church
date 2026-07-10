<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow any authenticated user; policy will handle permissions
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:members,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date'],
            'marital_status' => ['required', 'in:single,married,widowed,divorced'],
            'address' => ['nullable', 'string'],
            'salvation_date' => ['nullable', 'date'],
            'baptism_date' => ['nullable', 'date'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'registration_type' => ['nullable', 'string', 'max:255'],
            'departments' => ['nullable', 'array'],
            'departments.*' => ['exists:departments,id'],
        ];
    }
}
