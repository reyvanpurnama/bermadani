@extends('layouts.admin')

@section('title', '{{ $title }}')
@section('page-title', '{{ $title }}')

@section('content')
<div class="flex items-center justify-center h-full">
    <div class="text-center">
        <div class="text-6xl mb-4">🚧</div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">{{ $title }}</h2>
        <p class="text-slate-500">Halaman ini sedang dalam pengembangan</p>
    </div>
</div>
@endsection
