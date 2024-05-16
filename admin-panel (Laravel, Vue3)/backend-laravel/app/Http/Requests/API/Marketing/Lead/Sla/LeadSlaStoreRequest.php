<?php

namespace App\Http\Requests\API\Marketing\Lead\Sla;

use App\Models\LeadSla;
use App\Models\LeadStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadSlaStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'color' => 'required|string',
            'lead_status' => [
                'required',
                'string',
                Rule::exists(LeadStatus::class, 'slug')
                    ->where(function ($query) {
                    $query->where('slug', $this->lead_status);
                })
            ],
            'time_from' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'color.required' => 'Цвет обязателен',
            'color.string' => 'Парамет цвет является строкой',

            'lead_status.required' => 'Статус лида обязателен',
            'lead_status.string' => 'Необходим slug статуса лида в формате строки',
            'lead_status.exists' => 'Указанный статус лида не существует',

            'time_from.required' => 'Время обязательно',
            'time_from.numeric' => 'Время числовой параметр',
        ];
    }
}
