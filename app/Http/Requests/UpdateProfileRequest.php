<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string|max:255',
            'date_of_birth' => 'sometimes|nullable|date|before:today',
            'bio' => 'sometimes|nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.string' => 'رقم الهاتف لازم يكون نص',
            'phone.max' => 'رقم الهاتف لازم ما يزيد عن :max حرف',
            'address.string' => 'العنوان لازم يكون نص',
            'address.max' => 'العنوان لازم ما يزيد عن :max حرف',
            'date_of_birth.date' => 'تاريخ الميلاد لازم يكون تاريخ صحيح',
            'date_of_birth.before' => 'تاريخ الميلاد لازم يكون قبل اليوم',
            'bio.string' => 'النبذة لازم تكون نص',
            'bio.max' => 'النبذة لازم ما تزيد عن :max حرف',
        ];
    }
}
