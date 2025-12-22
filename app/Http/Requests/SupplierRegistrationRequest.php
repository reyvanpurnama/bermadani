<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public registration, anyone can register
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ownerName' => 'required|string|max:255',
            'businessName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:suppliers,email|unique:users,email',
            'address' => 'required|string',
            'description' => 'nullable|string|max:1000',
            'productCategory' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'registrationPaymentProof' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'ownerName.required' => 'Nama pemilik wajib diisi.',
            'businessName.required' => 'Nama usaha wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'address.required' => 'Alamat wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'registrationPaymentProof.required' => 'Bukti pembayaran wajib diunggah.',
            'registrationPaymentProof.file' => 'File harus berupa file yang valid.',
            'registrationPaymentProof.mimes' => 'Format file harus jpeg, png, jpg, gif, atau webp.',
            'registrationPaymentProof.max' => 'Ukuran file maksimal 5MB.',
        ];
    }
}
