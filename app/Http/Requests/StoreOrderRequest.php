<?php

namespace App\Http\Requests;

use App\Models\TicketType;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'ticket_type_id' => ['required', 'integer', 'exists:ticket_types,id'],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:' . config('harfest.max_qty_per_order', 10),
            ],
        ];
    }

    /**
     * Configure conditional validation for student ID and card.
     */
    public function withValidator($validator): void
    {
        $validator->sometimes(['student_id'], ['required', 'string', 'max:30'], function ($input) {
            $type = TicketType::find($input->ticket_type_id);
            return $type && $type->requires_student_id;
        });

        $validator->sometimes(['student_card'], ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], function ($input) {
            $type = TicketType::find($input->ticket_type_id);
            return $type && $type->requires_student_id;
        });
    }

    /**
     * Custom validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ticket_type_id.required' => 'Kategori tiket wajib dipilih.',
            'ticket_type_id.exists' => 'Kategori tiket tidak valid.',
            'quantity.required' => 'Jumlah tiket wajib diisi.',
            'quantity.integer' => 'Jumlah tiket harus berupa angka bulat.',
            'quantity.min' => 'Jumlah tiket minimal 1 paket.',
            'quantity.max' => 'Maksimal pembelian ' . config('harfest.max_qty_per_order', 10) . ' paket per pesanan.',
            'student_id.required' => 'NIM / Nomor Identitas wajib diisi untuk kategori Civitas.',
            'student_id.max' => 'NIM / Nomor Identitas maksimal 30 karakter.',
            'student_card.required' => 'Foto KTM / Kartu Identitas wajib diunggah untuk kategori Civitas.',
            'student_card.image' => 'File KTM harus berupa gambar.',
            'student_card.mimes' => 'Format foto KTM harus JPG, JPEG, atau PNG.',
            'student_card.max' => 'Ukuran foto KTM maksimal 2 MB.',
        ];
    }
}
