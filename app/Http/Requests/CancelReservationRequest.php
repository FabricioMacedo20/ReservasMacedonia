<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelReservationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'reservation_id' => 'required|exists:reservations,id',
        ];
    }

    public function messages()
    {
        return [
            'reservation_id.required' => 'O ID da reserva é obrigatório.',
            'reservation_id.exists' => 'A reserva especificada não existe.',
        ];
    }
}