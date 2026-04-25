@extends('layouts.admin')

@section('title', 'Point of Sale')
@section('page-title', 'Point of Sale')
@section('hide-navbar', true)
@section('main-class', 'p-0 flex')

@section('content')
    @livewire('pos-custom')
@endsection

@push('scripts')
<script>
    // Mobile cart toggle
    const toggleCartBtn = document.getElementById('toggle-cart-btn');
    const closeCartBtn = document.getElementById('close-cart-btn');
    const posCart = document.getElementById('pos-cart');
    const receiptBaseUrl = @json(auth()->user()->isKasir() ? url('/kasir/transaction') : url('/admin/transaction'));

    toggleCartBtn?.addEventListener('click', () => {
        posCart?.classList.remove('translate-y-full');
    });

    closeCartBtn?.addEventListener('click', () => {
        posCart?.classList.add('translate-y-full');
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'F2') {
            event.preventDefault();
            document.getElementById('pos-search-input')?.focus();
        }

        if (event.key === 'F9') {
            event.preventDefault();
            document.getElementById('pos-pay-btn')?.click();
        }

        if (event.key === 'Escape') {
            document.getElementById('pos-cancel-payment-btn')?.click();
        }
    });

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('open-receipt', (payload) => {
            const data = Array.isArray(payload) ? payload[0] : payload;
            const transactionId = data?.transactionId;

            if (!transactionId) {
                return;
            }

            window.open(`${receiptBaseUrl}/${transactionId}/receipt`, '_blank', 'width=420,height=720');
        });
    });
</script>
@endpush
