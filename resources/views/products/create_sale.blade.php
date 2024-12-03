@extends('layouts.app')

@section('title', 'Input Penjualan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Input Penjualan Produk</h1>
        </div>

        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('products.store-sale') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                <select name="product_id" id="product_id" required 
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                            {{ $product->name }} - {{ $product->brand }} (Stok: {{ $product->stock }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" min="1" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="tanggal_jual" class="block text-sm font-medium text-gray-700">Tanggal Jual</label>
                <input type="date" name="tanggal_jual" id="tanggal_jual" required
                       value="{{ date('Y-m-d') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('products.sold') }}" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('jumlah').addEventListener('input', function() {
    const productSelect = document.getElementById('product_id');
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const stock = selectedOption.dataset.stock;
    
    if (this.value > parseInt(stock)) {
        alert('Jumlah melebihi stok yang tersedia!');
        this.value = stock;
    }
});
</script>
@endpush
@endsection