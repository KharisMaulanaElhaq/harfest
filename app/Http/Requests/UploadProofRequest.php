<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadProofRequest extends FormRequest
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
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'bank_name' => ['nullable', 'string', 'max:50'],
            'sender_name' => ['nullable', 'string', 'max:100'],
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
            'proof.required' => 'File bukti transfer wajib diunggah.',
            'proof.file' => 'File bukti transfer tidak valid.',
            'proof.mimes' => 'Format bukti transfer harus berupa JPG, JPEG, PNG, atau PDF.',
            'proof.max' => 'Ukuran file bukti transfer maksimal 2 MB.',
            'bank_name.max' => 'Nama bank pengirim maksimal 50 karakter.',
            'sender_name.max' => 'Nama pemilik rekening pengirim maksimal 100 karakter.',
        ];
    }
}
