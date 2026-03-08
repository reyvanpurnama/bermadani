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
     * Notifikasi ke supplier: kasir konfirmasi terima barang (bisa ada selisih qty)
     */
    public static function notifyBatchReceived($supplierId, $batchCode, $receivedItems)
    {
        $totalRequested = array_sum(array_column($receivedItems, 'requestedQty'));
        $totalReceived  = array_sum(array_column($receivedItems, 'receivedQty'));
        $hasDiscrepancy = $totalReceived !== $totalRequested;

        $message = "Batch #{$batchCode} telah diterima oleh kasir kami. "
            . "Diminta: {$totalRequested} pcs, Diterima: {$totalReceived} pcs. ";

        if ($hasDiscrepancy) {
            $selisih = $totalRequested - $totalReceived;
            $message .= "Terdapat selisih {$selisih} pcs (rusak/tidak layak jual). ";
        }

        $message .= 'Terima kasih! 🙏';

        return self::create([
            'supplierId' => $supplierId,
            'type'       => 'BATCH_RECEIVED',
            'title'      => $hasDiscrepancy ? '⚠️ Barang Diterima (Ada Selisih)' : '✅ Barang Diterima!',
            'message'    => $message,
            'icon'       => $hasDiscrepancy ? 'bx-error' : 'bx-check-circle',
            'actionUrl'  => '/supplier/restock',
        ]);
    }

    /**
     * Notifikasi ke supplier: kasir submit laporan harian penjualan
     */
    public static function notifyDailyReport($supplierId, string $date, int $totalQty, float $totalOmzet, float $totalPayable)
    {
        return self::create([
            'supplierId' => $supplierId,
            'type'       => 'DAILY_REPORT',
            'title'      => '📊 Laporan Penjualan Harian',
            'message'    => "Laporan penjualan {$date}: produk Anda terjual {$totalQty} pcs, "
                . 'total omzet Rp ' . number_format($totalOmzet, 0, ',', '.') . ', '
                . 'hak Anda Rp ' . number_format($totalPayable, 0, ',', '.') . '. '
                . 'Detail tersedia di portal supplier. 🙏',
            'icon'       => 'bx-bar-chart-alt-2',
            'actionUrl'  => '/supplier/sales',
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
