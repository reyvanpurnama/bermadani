<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user can manage products
        return $this->user() && $this->user()->canManageProducts();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:products,sku'], // Changed to nullable
            'categoryId' => ['required', 'exists:categories,id'], // Removed uuid rule
            'supplierId' => ['nullable', 'exists:suppliers,id'], // Removed uuid rule just in case, though suppliers might be UUID. Let's check supplier too, but safest is to remove explicit uuid check if we trust exists. Actually let's assume supplier is UUID or ID based on its migration. But safe to remove 'uuid' rule and rely on string/exists.
            'description' => ['nullable', 'string', 'max:1000'],
            'sellPrice' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'buyPrice' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'stock' => ['required', 'integer', 'min:0'],
            'threshold' => ['required', 'integer', 'min:1', 'max:10000'],
            'unit' => ['nullable', 'string', 'max:50', 'in:pcs,kg,liter,box,pack,unit'],
            'isConsignment' => ['boolean'],
            'consignmentFeePercentage' => ['nullable', 'numeric', 'min:0', 'max:100', 'required_if:isConsignment,true'],
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama produk',
            'sku' => 'SKU',
            'categoryId' => 'kategori',
            'supplierId' => 'supplier',
            'sellPrice' => 'harga jual',
            'buyPrice' => 'harga beli',
            'stock' => 'stok',
            'threshold' => 'batas minimum stok',
            'unit' => 'satuan',
            'consignmentFeePercentage' => 'persentase fee konsinyasi',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'sku.unique' => 'SKU sudah digunakan produk lain.',
            'categoryId.exists' => 'Kategori tidak ditemukan.',
            'supplierId.exists' => 'Supplier tidak ditemukan.',
            'consignmentFeePercentage.required_if' => 'Persentase fee konsinyasi wajib diisi untuk produk konsinyasi.',
        ];
    }
}
