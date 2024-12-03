@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Input Produk Terjual</h1>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <form action="{{ route('produk-terjual.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Produk
                </label>
                <select name="stock_id" class="shadow border rounded w-full py-2 px-3">
                    @foreach($stocks as $stock)
                    <option value="{{ $stock->id }}">
                        {{ $stock->name }} (Stok: {{ $stock->stock }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Jumlah
                </label>
                <input type="number" name="jumlah" class="shadow border rounded w-full py-2 px-3" min="1">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Tanggal Penjualan
                </label>
                <input type="date" name="tanggal_jual" class="shadow border rounded w-full py-2 px-3">
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                Simpan
            </button>
        </form>
    </div>
</div>
@endsection