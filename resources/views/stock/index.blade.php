@extends('layouts.app')

@section('title', 'Stock Management')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Stock Management</h1>
            @if(auth()->user()->role === 'warehouse' || auth()->user()->role === 'owner')
            <a href="{{ route('warehouse.input-barang') }}" 
                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                Tambah Barang
            </a>
            @endif
        </div>

        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-700">Total Produk</h3>
                <p class="text-2xl font-bold text-blue-800">{{ $products->count() }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-red-700">Stok Menipis</h3>
                <p class="text-2xl font-bold text-red-800">
                    {{ $products->where('stock', '<=', 'minimum_stock')->count() }}
                </p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-green-700">Stok Tersedia</h3>
                <p class="text-2xl font-bold text-green-800">
                    {{ $products->where('stock', '>', 'minimum_stock')->count() }}
                </p>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
            <form action="{{ route('products.search') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                        <input type="text" 
                               id="search"
                               name="search" 
                               placeholder="Cari nama produk..."
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               value="{{ request('search') }}">
                    </div>
                    
                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Merek</label>
                        <input type="text" 
                               id="brand"
                               name="brand" 
                               placeholder="Filter by merek..."
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               value="{{ request('brand') }}">
                    </div>
                    
                    <div>
                        <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Kendaraan</label>
                        <input type="text" 
                               id="vehicle_type"
                               name="vehicle_type" 
                               placeholder="Filter by tipe kendaraan..."
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               value="{{ request('vehicle_type') }}">
                    </div>
                    
                    <div>
                        <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-1">Status Stok</label>
                        <select name="stock_status" 
                                id="stock_status"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Semua Status</option>
                            <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stok Menipis</option>
                            <option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-search mr-2"></i> Cari
                    </button>
                    @if(request()->hasAny(['search', 'brand', 'vehicle_type', 'stock_status']))
                        <a href="{{ route('stock') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <i class="fas fa-undo mr-2"></i> Reset
                        </a>
                    @endif
                </div>
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
                            <a href="{{ route('stock', ['sort' => 'name', 'order' => request('sort') === 'name' && request('order') === 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'order'])) }}" 
                               class="flex items-center space-x-1 group">
                                <span>Nama Produk</span>
                                @if(request('sort') === 'name')
                                    <svg class="w-4 h-4 {{ request('order') === 'asc' ? '' : 'transform rotate-180' }}" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M7 10l5 5 5-5z"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('stock', ['sort' => 'brand', 'order' => request('sort') === 'brand' && request('order') === 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'order'])) }}" 
                               class="flex items-center space-x-1 group">
                                <span>Merek</span>
                                @if(request('sort') === 'brand')
                                    <svg class="w-4 h-4 {{ request('order') === 'asc' ? '' : 'transform rotate-180' }}" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M7 10l5 5 5-5z"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('stock', ['sort' => 'vehicle_type', 'order' => request('sort') === 'vehicle_type' && request('order') === 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'order'])) }}" 
                               class="flex items-center space-x-1 group">
                                <span>Tipe Kendaraan</span>
                                @if(request('sort') === 'vehicle_type')
                                    <svg class="w-4 h-4 {{ request('order') === 'asc' ? '' : 'transform rotate-180' }}" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M7 10l5 5 5-5z"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('stock', ['sort' => 'stock', 'order' => request('sort') === 'stock' && request('order') === 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'order'])) }}" 
                               class="flex items-center space-x-1 group">
                                <span>Stok</span>
                                @if(request('sort') === 'stock')
                                    <svg class="w-4 h-4 {{ request('order') === 'asc' ? '' : 'transform rotate-180' }}" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M7 10l5 5 5-5z"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('stock', ['sort' => 'price', 'order' => request('sort') === 'price' && request('order') === 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'order'])) }}" 
                               class="flex items-center space-x-1 group">
                                <span>Harga</span>
                                @if(request('sort') === 'price')
                                    <svg class="w-4 h-4 {{ request('order') === 'asc' ? '' : 'transform rotate-180' }}" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M7 10l5 5 5-5z"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('stock', ['sort' => 'updated_at', 'order' => request('sort') === 'updated_at' && request('order') === 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'order'])) }}" 
                               class="flex items-center space-x-1 group">
                                <span>Terakhir Update</span>
                                @if(request('sort') === 'updated_at')
                                    <svg class="w-4 h-4 {{ request('order') === 'asc' ? '' : 'transform rotate-180' }}" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M7 10l5 5 5-5z"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $index => $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->brand }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->vehicle_type }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($product->stock <= $product->minimum_stock)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Stok Menipis
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Tersedia
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->updated_at->format('d/m/Y H:i:s') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            Tidak ada data produk
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($products, 'hasPages') && $products->hasPages())
        <div class="mt-4">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Auto submit form when status changes
    document.querySelector('#stock_status').addEventListener('change', function() {
        this.form.submit();
    });

    // Auto submit form when pressing enter in any input
    const inputs = document.querySelectorAll('input[type="text"]');
    inputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });
    });

    // Hover effects for sort headers
    const sortHeaders = document.querySelectorAll('th a');
    sortHeaders.forEach(header => {
        header.addEventListener('mouseover', function() {
            this.classList.add('text-indigo-600');
        });
        header.addEventListener('mouseout', function() {
            this.classList.remove('text-indigo-600');
        });
    });
</script>
@endpush
@endsection