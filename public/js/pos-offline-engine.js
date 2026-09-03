/**
 * KSPPS Berkah Madani - POS Offline Engine
 * IndexedDB storage via Dexie.js & Background Auto-Sync Engine
 */

window.PosOfflineEngine = (function () {
    // 1. Initialize Dexie Database
    const db = new Dexie('KSPPS_POS_Database');
    db.version(1).stores({
        products: 'id, barcode, name, sellPrice, categoryId',
        members: 'id, name, nomorAnggota',
        offline_queue: '++id, offlineToken, status, created_at'
    });

    let isOnline = navigator.onLine;
    let isSyncing = false;

    // Helper CSRF Token
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    // 2. Fetch & Cache Product & Member Catalog
    async function syncCatalog() {
        if (!navigator.onLine) return;
        try {
            const response = await fetch('/admin/api/pos/catalog', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });
            if (response.ok) {
                const data = await response.json();
                if (data.products && data.products.length > 0) {
                    await db.products.clear();
                    await db.products.bulkPut(data.products);
                    console.log(`[POS Offline Engine] Catalog synced: ${data.products.length} products`);
                }
                if (data.members && data.members.length > 0) {
                    await db.members.clear();
                    await db.members.bulkPut(data.members);
                    console.log(`[POS Offline Engine] Members synced: ${data.members.length} members`);
                }
            }
        } catch (err) {
            console.warn('[POS Offline Engine] Catalog sync failed (offline mode active):', err);
        }
    }

    // 3. Search Products in Client-Side IndexedDB (0ms Barcode Search)
    async function searchProducts(query) {
        if (!query || query.trim() === '') {
            return await db.products.limit(50).toArray();
        }
        const q = query.toLowerCase().trim();
        
        // Match exact barcode first
        const exactBarcode = await db.products.where('barcode').equals(q).first();
        if (exactBarcode) return [exactBarcode];

        // Search by name or barcode
        return await db.products
            .filter(p => p.name.toLowerCase().includes(q) || (p.barcode && p.barcode.toLowerCase().includes(q)))
            .limit(30)
            .toArray();
    }

    // 4. Save Transaction to Offline Queue
    async function saveOfflineTransaction(trxPayload) {
        const record = {
            offlineToken: 'OFF-' + Date.now() + '-' + Math.random().toString(36).substring(2, 7),
            cart: trxPayload.cart,
            memberId: trxPayload.memberId || null,
            userId: trxPayload.userId || 1,
            paymentMethod: trxPayload.paymentMethod || 'CASH',
            cashReceived: trxPayload.cashReceived || 0,
            note: trxPayload.note || '',
            totalAmount: trxPayload.totalAmount || 0,
            status: 'PENDING',
            created_at: new Date().toISOString()
        };

        const id = await db.offline_queue.add(record);
        console.log('[POS Offline Engine] Transaction saved to offline queue:', record.offlineToken);
        
        updateQueueBadge();
        return record;
    }

    // 5. Sync Offline Queue to Server
    async function syncOfflineQueue() {
        if (!navigator.onLine || isSyncing) return;
        
        const pendingList = await db.offline_queue.where('status').equals('PENDING').toArray();
        if (pendingList.length === 0) {
            updateQueueBadge();
            return;
        }

        isSyncing = true;
        console.log(`[POS Offline Engine] Syncing ${pendingList.length} offline transactions...`);

        try {
            const response = await fetch('/admin/api/pos/sync-offline', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ transactions: pendingList })
            });

            if (response.ok) {
                const resData = await response.json();
                if (resData.synced && resData.synced.length > 0) {
                    for (const item of resData.synced) {
                        await db.offline_queue.where('offlineToken').equals(item.offlineToken).modify({ status: 'SYNCED' });
                    }
                    // Clean up synced items
                    await db.offline_queue.where('status').equals('SYNCED').delete();
                    console.log('[POS Offline Engine] Sync complete!');
                    
                    if (window.Livewire) {
                        window.Livewire.dispatch('notify', {
                            type: 'success',
                            message: `✅ ${resData.syncedCount} transaksi offline berhasil disinkronisasi ke server!`
                        });
                    }
                }
            }
        } catch (err) {
            console.warn('[POS Offline Engine] Queue sync failed, will retry when online:', err);
        } finally {
            isSyncing = false;
            updateQueueBadge();
        }
    }

    // 6. Update Queue Counter Badge on UI
    async function updateQueueBadge() {
        try {
            const pendingCount = await db.offline_queue.where('status').equals('PENDING').count();
            const badgeEl = document.getElementById('offline-queue-badge');
            if (badgeEl) {
                if (pendingCount > 0) {
                    badgeEl.innerText = `${pendingCount} Transaksi Offline Pending 🔄`;
                    badgeEl.classList.remove('hidden');
                } else {
                    badgeEl.classList.add('hidden');
                }
            }
        } catch (e) {
            // Ignore if DB not ready
        }
    }

    // 7. Network Status Listeners
    window.addEventListener('online', () => {
        isOnline = true;
        document.body.classList.remove('offline-mode');
        const badge = document.getElementById('pos-connection-status');
        if (badge) {
            badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 flex items-center gap-1.5';
            badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span> ONLINE 🟢';
        }
        syncCatalog();
        syncOfflineQueue();
    });

    window.addEventListener('offline', () => {
        isOnline = false;
        document.body.classList.add('offline-mode');
        const badge = document.getElementById('pos-connection-status');
        if (badge) {
            badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 flex items-center gap-1.5';
            badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500"></span> OFFLINE 📡';
        }
    });

    // 8. Open Thermal Receipt Printing Window
    function printReceipt(transactionId) {
        if (!transactionId) return;
        const receiptUrl = `/admin/transaction/${transactionId}/receipt`;
        const printWindow = window.open(receiptUrl, 'ReceiptPrint', 'width=350,height=600,top=100,left=100');
        if (printWindow) {
            printWindow.focus();
        }
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => {
        syncCatalog();
        syncOfflineQueue();
        updateQueueBadge();
    });

    return {
        db,
        syncCatalog,
        searchProducts,
        saveOfflineTransaction,
        syncOfflineQueue,
        updateQueueBadge,
        printReceipt,
        isOnline: () => isOnline
    };
})();
