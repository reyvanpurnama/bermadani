<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('id'); // Get product ID from route
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'categoryId' => ['required', 'uuid', 'exists:categories,id'],
            'supplierId' => ['nullable', 'uuid', 'exists:suppliers,id'],
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
