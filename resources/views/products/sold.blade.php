@extends('layouts.app')

@section('title', 'Daftar Penjualan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Daftar Penjualan</h1>
            <a href="{{ route('products.create-sale') }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                Tambah Penjualan
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-700">Total Transaksi</h3>
                <p class="text-2xl font-bold text-blue-800">{{ $produkTerjual->count() }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-green-700">Total Produk Terjual</h3>
                <p class="text-2xl font-bold text-green-800">{{ $produkTerjual->sum('jumlah') }}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-700">Total Jenis Produk</h3>
                <p class="text-2xl font-bold text-purple-800">{{ $produkTerjual->unique('product_id')->count() }}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-yellow-700">Total Pendapatan</h3>
                <p class="text-2xl font-bold text-yellow-800">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="mb-6">
            <form action="{{ route('products.searchSold') }}" method="GET" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           placeholder="Cari nama produk atau brand..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           value="{{ request('search') }}">
                </div>
                <div class="flex-1">
                    <input type="date" 
                           name="date" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           value="{{ request('date') }}">
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Cari
                </button>
                @if(request('search') || request('date'))
                    <a href="{{ route('products.sold') }}" 
                       class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Produk
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Brand
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah Terjual
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Harga
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sisa Stok
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($produkTerjual as $index => $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ ($produkTerjual->currentPage() - 1) * $produkTerjual->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($sale->tanggal_jual)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $sale->product->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $sale->product->brand }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                {{ $sale->jumlah }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                Rp {{ number_format($sale->harga, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $sale->product->stock > 10 ? 'bg-green-100 text-green-800' : 
                                   ($sale->product->stock > 0 ? 'bg-nayellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $sale->product->stock }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            Tidak ada data penjualan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($produkTerjual->hasPages())
        <div class="mt-4">
            {{ $produkTerjual->links() }}
        </div>
        @endif

        <!-- Export Button -->
        @if($produkTerjual->count() > 0)
        <div class="mt-6 flex justify-end">
            <a href="{{ route('products.exportPdf', request()->all()) }}" 
               class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                Download Resi PDF
            </a>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.querySelector('input[name="date"]').addEventListener('change', function() {
        if(this.value) {
            this.form.submit();
        }
    });
</script>
@endpush
@endsection