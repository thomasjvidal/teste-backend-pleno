<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'address.zip_code' => 'required|string|regex:/^\d{5}-?\d{3}$/',
            'address.address_number' => 'required|string|max:20',
            'address.country' => 'nullable|string|max:100',
            'address.state' => 'nullable|string|max:100',
            'address.street_address' => 'nullable|string|max:255',
            'address.city' => 'nullable|string|max:100',
            'address.address_line' => 'nullable|string|max:255',
            'address.neighborhood' => 'nullable|string|max:100',
            'phones' => 'required|array|min:1|max:5',
            'phones.*.phone' => 'required|string|regex:/^\(\d{2}\)\s\d{4,5}-?\d{4}$/',
            'emails' => 'required|array|min:1|max:5',
            'emails.*.email' => 'required|email|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do contato é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'description.required' => 'A descrição do contato é obrigatória.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'address.zip_code.required' => 'O CEP é obrigatório.',
            'address.zip_code.regex' => 'O CEP deve estar no formato 00000-000.',
            'address.address_number.required' => 'O número do endereço é obrigatório.',
            'phones.required' => 'Pelo menos um telefone é obrigatório.',
            'phones.min' => 'Pelo menos um telefone é obrigatório.',
            'phones.max' => 'Máximo de 5 telefones permitidos.',
            'phones.*.phone.required' => 'O número do telefone é obrigatório.',
            'phones.*.phone.regex' => 'O telefone deve estar no formato (00) 00000-0000.',
            'emails.required' => 'Pelo menos um email é obrigatório.',
            'emails.min' => 'Pelo menos um email é obrigatório.',
            'emails.max' => 'Máximo de 5 emails permitidos.',
            'emails.*.email.required' => 'O email é obrigatório.',
            'emails.*.email.email' => 'O email deve ter um formato válido.',
        ];
    }
} 