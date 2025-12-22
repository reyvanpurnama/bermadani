<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Supplier can update their own profile, Admin can update any
        $user = $this->user();
        $supplier = auth()->guard('supplier')->user();
        
        if ($user && ($user->isAdmin() || $user->isSuperAdmin())) {
            return true;
        }
        
        if ($supplier) {
            return true;
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $supplierId = $this->route('id') ?? auth()->guard('supplier')->id();
        
        return [
            'ownerName' => ['required', 'string', 'max:255'],
            'businessName' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('suppliers', 'phone')->ignore($supplierId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('suppliers', 'email')->ignore($supplierId)],
            'address' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
            'productCategory' => ['required', 'string', 'max:255'],
            'bankName' => ['nullable', 'string', 'max:100'],
            'bankAccountNumber' => ['nullable', 'string', 'max:50'],
            'bankAccountHolderName' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'ownerName' => 'nama pemilik',
            'businessName' => 'nama usaha',
            'phone' => 'nomor telepon',
            'email' => 'email',
            'address' => 'alamat',
            'description' => 'deskripsi',
            'productCategory' => 'kategori produk',
            'bankName' => 'nama bank',
            'bankAccountNumber' => 'nomor rekening',
            'bankAccountHolderName' => 'nama pemilik rekening',
            'password' => 'password',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Remove password if empty (not changing password)
        if ($this->password === '' || $this->password === null) {
            $this->request->remove('password');
            $this->request->remove('password_confirmation');
        }
    }
}
