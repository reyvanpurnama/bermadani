<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierNotification extends Model
{
    protected $fillable = [
        'supplierId',
        'type',
        'title',
        'message',
        'icon',
        'actionUrl',
        'isRead',
        'readAt',
    ];

    protected $casts = [
        'isRead' => 'boolean',
        'readAt' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplierId');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'isRead' => true,
            'readAt' => now(),
        ]);
    }

    /**
     * Create a batch request notification for supplier
     */
    public static function notifyBatchRequest($supplierId, $batchCode, $items)
    {
        $itemCount = count($items);
        $totalQty = array_sum(array_column($items, 'initialQty'));
        
        // Calculate min/max tolerance (80% - 120%)
        $minQty = (int) floor($totalQty * 0.8);
        $maxQty = (int) ceil($totalQty * 1.2);

        return self::create([
            'supplierId' => $supplierId,
            'type' => 'BATCH_REQUEST',
            'title' => '📦 Permintaan Barang Baru!',
            'message' => "Halo! Koperasi membutuhkan {$itemCount} jenis produk Anda (total {$totalQty} pcs) untuk batch #{$batchCode}. "
                . "Mohon siapkan barang sesuai jumlah yang diminta ya. "
                . "Catatan: Usahakan pas, tidak boleh kurang dari {$minQty} pcs dan tidak boleh lebih dari {$maxQty} pcs. "
                . "Terima kasih atas kerjasamanya! 🙏",
            'icon' => 'bx-package',
            'actionUrl' => '/supplier/konsinyasi',
        ]);
    }
}
