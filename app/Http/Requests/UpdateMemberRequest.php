<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
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
        $member = $this->route('member');
        
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:members,email,' . $member->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'marital_status' => ['required', 'in:single,married,widowed,divorced'],
            'wedding_date' => ['nullable', 'date', 'before_or_equal:today'],
            'address' => ['nullable', 'string'],
            'salvation_date' => ['nullable', 'date', 'before_or_equal:today'],
            'baptism_date' => ['nullable', 'date', 'before_or_equal:today'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'in:active,inactive,pending'],
            'departments' => ['nullable', 'array'],
            'departments.*' => ['exists:departments,id'],
        ];
    }
}
