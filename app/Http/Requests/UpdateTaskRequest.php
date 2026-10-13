<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'title' => 'sometimes|string|max:40',
            'description' => 'sometimes|nullable|string',
            'priority' => 'sometimes|in:high,medium,low',
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
            'title.string' => 'عنوان المهمة لازم يكون نص',
            'title.max' => 'عنوان المهمة لازم ما يزيد عن 40 حرف',
            'description.string' => 'وصف المهمة لازم يكون نص',
            'priority.in' => 'أولوية المهمة لازم تكون high أو medium أو low',
        ];
    }
}
