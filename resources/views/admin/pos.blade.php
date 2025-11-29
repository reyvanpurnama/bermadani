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

    toggleCartBtn?.addEventListener('click', () => {
        posCart?.classList.remove('translate-y-full');
    });

    closeCartBtn?.addEventListener('click', () => {
        posCart?.classList.add('translate-y-full');
    });
</script>
@endpush
