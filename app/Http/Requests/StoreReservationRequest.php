<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'table_id' => 'required|exists:tables,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'customer_name' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
        ];
    }

    public function messages()
    {
        return [
            'table_id.required' => 'A mesa é obrigatória.',
            'table_id.exists' => 'A mesa selecionada não existe.',
            'time_slot_id.required' => 'O horário é obrigatório.',
            'time_slot_id.exists' => 'O horário selecionado não existe.',
            'customer_name.required' => 'O nome do cliente é obrigatório.',
            'customer_name.string' => 'O nome do cliente deve ser uma string.',
            'customer_name.max' => 'O nome do cliente não pode ter mais de 255 caracteres.',
            'date.required' => 'A data é obrigatória.',
            'date.date' => 'A data deve ser uma data válida.',
            'date.after_or_equal' => 'A data deve ser hoje ou uma data futura.',
        ];
    }
}