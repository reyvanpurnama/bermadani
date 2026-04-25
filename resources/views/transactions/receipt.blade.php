<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $transaction->invoiceNumber }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 20px;
            max-width: 300px;
            margin: 0 auto;
        }
        
        .receipt {
            border: 1px solid #000;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .store-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .store-info {
            font-size: 10px;
            margin-bottom: 2px;
        }
        
        .transaction-info {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .items-table {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .item-row {
            margin-bottom: 8px;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        
        .totals {
            margin-top: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 10px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .receipt {
                border: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        {{-- Header --}}
        <div class="header">
            <div class="store-name">KOPERASI UMB</div>
            <div class="store-info">Universitas Muhammadiyah Bengkulu</div>
            <div class="store-info">Jl. Bali, Bengkulu</div>
            <div class="store-info">Telp: (0736) 22765</div>
        </div>

        {{-- Transaction Info --}}
        <div class="transaction-info">
            <div class="info-row">
                <span>Invoice:</span>
                <span><strong>{{ $transaction->invoiceNumber }}</strong></span>
            </div>
            <div class="info-row">
                <span>Tanggal:</span>
                <span>{{ $transaction->date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span>Kasir:</span>
                <span>{{ auth()->user()->name ?? 'System' }}</span>
            </div>
            @if($transaction->member)
                <div class="info-row">
                    <span>Member:</span>
                    <span>{{ $transaction->member->name }}</span>
                </div>
                <div class="info-row">
                    <span>No. Anggota:</span>
                    <span>{{ $transaction->member->nomorAnggota }}</span>
                </div>
            @endif
            <div class="info-row">
                <span>Pembayaran:</span>
                <span>
                    @if($transaction->paymentMethod === 'CASH') 💵 Tunai
                    @elseif($transaction->paymentMethod === 'TRANSFER') 🏦 Transfer
                    @elseif($transaction->paymentMethod === 'SUKARELA') 👛 Simpanan
                    @else 💳 Kredit
                    @endif
                </span>
            </div>
        </div>

        {{-- Items --}}
        <div class="items-table">
            @foreach($transaction->items as $item)
                <div class="item-row">
                    <div class="item-name">{{ $item->product->name }}</div>
                    <div class="item-details">
                        <span>{{ $item->quantity }} x Rp {{ number_format($item->unitPrice, 0, ',', '.') }}</span>
                        <span><strong>Rp {{ number_format($item->totalPrice, 0, ',', '.') }}</strong></span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Totals --}}
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($transaction->items->sum('totalPrice'), 0, ',', '.') }}</span>
            </div>
            
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaction->totalAmount, 0, ',', '.') }}</span>
            </div>

            @if($transaction->note)
                <div class="info-row" style="margin-top: 10px; font-size: 10px;">
                    <span>Catatan:</span>
                    <span>{{ $transaction->note }}</span>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
            <p style="margin-top: 10px;">www.koperasiumb.com</p>
            <p style="margin-top: 10px; font-size: 9px;">Printed: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    {{-- Print button (hidden on print) --}}
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 14px; cursor: pointer; background: #4CAF50; color: white; border: none; border-radius: 5px;">
            🖨️ Print Receipt
        </button>
        <button onclick="window.close()" style="padding: 10px 30px; font-size: 14px; cursor: pointer; background: #666; color: white; border: none; border-radius: 5px; margin-left: 10px;">
            ❌ Close
        </button>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
