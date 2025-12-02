@extends('layouts.admin')

@section('title', 'Detail Transaksi Manual')

@section('content')
    @livewire('manual-transaction-detail', ['transactionId' => $transactionId])
@endsection
