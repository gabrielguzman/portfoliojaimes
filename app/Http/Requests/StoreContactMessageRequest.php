<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:160'], 'message' => ['required', 'string', 'min:10', 'max:5000'],
            'consent' => ['accepted'], 'website' => ['nullable', 'string', 'max:0']];
    }

    public function messages(): array
    {
        return ['name.required' => 'Escribí tu nombre.', 'email.required' => 'Escribí tu correo.', 'email.email' => 'Ingresá un correo válido.',
            'subject.required' => 'Escribí el asunto de tu consulta.', 'message.required' => 'Escribí tu mensaje.',
            'message.min' => 'Contanos un poco más: el mensaje debe tener al menos 10 caracteres.',
            'message.max' => 'El mensaje puede tener hasta 5000 caracteres.', 'consent.accepted' => 'Necesitamos tu autorización para guardar y gestionar esta consulta.',
            'website.max' => 'No pudimos enviar la consulta. Intentá nuevamente.'];
    }

    public function attributes(): array
    {
        return ['name' => 'nombre', 'email' => 'correo', 'subject' => 'asunto', 'message' => 'mensaje'];
    }
}
