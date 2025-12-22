<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Admin and Kasir can create transactions
        return $this->user() && (
            $this->user()->isAdmin() || 
            $this->user()->isKasir() || 
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
            'memberId' => ['nullable', 'uuid', 'exists:members,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.productId' => ['required', 'uuid', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.sellPrice' => ['required', 'numeric', 'min:0'],
            'paymentMethod' => ['required', 'string', 'in:CASH,DEBIT,CREDIT,QRIS,TRANSFER'],
            'paymentAmount' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'memberId' => 'member',
            'items' => 'item transaksi',
            'items.*.productId' => 'produk',
            'items.*.quantity' => 'jumlah',
            'items.*.sellPrice' => 'harga jual',
            'paymentMethod' => 'metode pembayaran',
            'paymentAmount' => 'jumlah pembayaran',
            'discount' => 'diskon',
            'tax' => 'pajak',
            'notes' => 'catatan',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Minimal harus ada 1 item untuk transaksi.',
            'items.*.productId.exists' => 'Produk tidak ditemukan.',
            'paymentMethod.in' => 'Metode pembayaran tidak valid.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty memberId to null
        if ($this->memberId === '') {
            $this->merge(['memberId' => null]);
        }
    }
}
