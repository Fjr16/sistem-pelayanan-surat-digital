<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonScheduleRequest extends FormRequest
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
            'grade_id' => 'required|exists:grades,id',
            'lesson_period_id' => 'required|array',
            'lesson_period_id.*' => 'required|exists:lesson_periods,id',
            'teacher_id' => 'required|array',
            'teacher_id.*' => 'nullable',
            'nama_kegiatan' => 'required|array',
            'nama_kegiatan.*' => 'nullable',
        ];
    }
}
