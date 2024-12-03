@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Data Produk Terjual</h1>
        <a href="{{ route('produk-terjual.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah Penjualan</a>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b-2">Nama Produk</th>
                    <th class="px-6 py-3 border-b-2">Jumlah</th>
                    <th class="px-6 py-3 border-b-2">Tanggal Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produkTerjual as $produk)
                <tr>
                    <td class="px-6 py-4">{{ $produk->stock->name }}</td>
                    <td class="px-6 py-4">{{ $produk->jumlah }}</td>
                    <td class="px-6 py-4">{{ $produk->tanggal_jual }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection