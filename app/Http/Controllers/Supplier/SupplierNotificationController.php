<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SupplierNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierNotificationController extends Controller
{
    public function index()
    {
        $supplier = Auth::guard('supplier')->user();
        
        $notifications = SupplierNotification::where('supplierId', $supplier->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('supplier.notifications', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $supplier = Auth::guard('supplier')->user();
        
        $notification = SupplierNotification::where('id', $id)
            ->where('supplierId', $supplier->id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    public function markAllAsRead()
    {
        $supplier = Auth::guard('supplier')->user();
        
        SupplierNotification::where('supplierId', $supplier->id)
            ->where('isRead', false)
            ->update([
                'isRead' => true,
                'readAt' => now(),
            ]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }
}
