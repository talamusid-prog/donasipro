@extends('layouts.app')
@section('title', 'Tunaikan Zakat Mal')
@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 py-10">
    <div class="bg-white rounded-xl shadow p-6 max-w-md w-full text-center">
        <h1 class="text-2xl font-bold mb-4 text-orange-600">Tunaikan Zakat Mal</h1>
        <p class="mb-6 text-gray-600">Silakan transfer zakat mal Anda ke rekening resmi kami atau klik tombol di bawah untuk konfirmasi pembayaran.</p>
        <a href="/" class="btn-calculate-green">Kembali ke Kalkulator</a>
    </div>
</div>

<style>
.btn-calculate-green {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    font-weight: 600;
    color: #fff;
    background: #f59e42;
    border-radius: 0.5rem;
    padding: 0.75rem 1.25rem;
    font-size: 1rem;
    box-shadow: 0 2px 8px 0 rgba(245,158,66,0.08);
    transition: background 0.2s, box-shadow 0.2s;
    margin-top: 1.5rem;
    margin-bottom: 0;
    text-align: center;
    min-height: 48px;
    gap: 0.5rem;
    border: none;
    text-decoration: none;
}
.btn-calculate-green:hover, .btn-calculate-green:focus {
    background: #ea580c;
    color: #fff;
}
</style>
@endsection 