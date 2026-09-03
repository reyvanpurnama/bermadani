<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Member;
use Illuminate\Http\JsonResponse;

class POSCatalogSyncController extends Controller
{
    public function getCatalog(): JsonResponse
    {
        $products = Product::where('status', 'ACTIVE')
            ->select('id', 'name', 'sku', 'sellPrice', 'buyPrice', 'stock', 'image', 'categoryId')
            ->get()
            ->map(function ($p) {
                $code = (string) ($p->sku ?? '');
                return [
                    'id' => (int) $p->id,
                    'name' => (string) $p->name,
                    'sku' => $code,
                    'barcode' => $code,
                    'sellPrice' => (float) $p->sellPrice,
                    'buyPrice' => (float) $p->buyPrice,
                    'stock' => (int) $p->stock,
                    'image' => (string) ($p->image ?? ''),
                    'categoryId' => (int) ($p->categoryId ?? 0),
                ];
            });

        $members = Member::where('status', 'ACTIVE')
            ->select('id', 'name', 'nomorAnggota', 'unitKerja', 'tier', 'points', 'simpananSukarela')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => (int) $m->id,
                    'name' => (string) $m->name,
                    'nomorAnggota' => (string) $m->nomorAnggota,
                    'unitKerja' => (string) ($m->unitKerja ?? ''),
                    'tier' => (string) $m->tier,
                    'points' => (int) $m->points,
                    'simpananSukarela' => (float) $m->simpananSukarela,
                ];
            });

        return response()->json([
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'products' => $products,
            'members' => $members,
        ]);
    }
}
