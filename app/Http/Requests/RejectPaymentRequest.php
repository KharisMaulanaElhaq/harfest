<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reject_reason' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reject_reason.required' => 'Alasan penolakan pembayaran wajib diisi agar pembeli mengetahui kesalahannya.',
            'reject_reason.min' => 'Alasan penolakan minimal 3 karakter.',
            'reject_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ];
    }
}
