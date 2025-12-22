<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinancialTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only Admin and Super Admin can manage financial transactions
        return $this->user() && (
            $this->user()->isAdmin() || 
            $this->user()->isSuperAdmin()
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:INCOME,EXPENSE'],
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999.99'],
            'description' => ['required', 'string', 'max:500'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'paymentMethod' => ['nullable', 'string', 'in:CASH,DEBIT,CREDIT,TRANSFER'],
            'referenceNumber' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'type' => 'tipe transaksi',
            'category' => 'kategori',
            'amount' => 'jumlah',
            'description' => 'deskripsi',
            'date' => 'tanggal',
            'paymentMethod' => 'metode pembayaran',
            'referenceNumber' => 'nomor referensi',
            'notes' => 'catatan',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'type.in' => 'Tipe transaksi harus INCOME atau EXPENSE.',
            'date.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
            'amount.min' => 'Jumlah minimal Rp 1.',
        ];
    }
}
