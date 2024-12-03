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
            
            <div id="product-container">
                <!-- First product entry -->
                <div class="product-entry mb-4 bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                            <select name="products[0][product_id]" 
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md product-select" 
                                    required>
                                <option value="">Pilih Produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-stock="{{ $product->stock }}" 
                                            data-price="{{ $product->price }}">
                                        {{ $product->name }} - {{ $product->brand }} (Stok: {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                            <input type="number" 
                                   name="products[0][jumlah]" 
                                   min="1" 
                                   required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm quantity-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Harga</label>
                            <input type="number" 
                                   name="products[0][harga]" 
                                   min="0" 
                                   required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm price-input">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="tanggal_jual" class="block text-sm font-medium text-gray-700">Tanggal Jual</label>
                <input type="date" 
                       name="tanggal_jual" 
                       id="tanggal_jual" 
                       required
                       value="{{ date('Y-m-d') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="flex justify-between items-center mt-6">
                <button type="button" 
                        id="add-product"
                        class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                    + Tambah Produk
                </button>
                
                <div class="flex space-x-4">
                    <a href="{{ route('products.sold') }}" 
                       class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Batal
                    </a>
                    <button type="submit" 
                            class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let productCount = 0;

function checkStock(input, stock) {
    if (parseInt(input.value) > parseInt(stock)) {
        alert('Jumlah melebihi stok yang tersedia!');
        input.value = stock;
    }
}

function createProductEntry(index) {
    return `
        <div class="product-entry mb-4 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                    <select name="products[${index}][product_id]" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md product-select" 
                            required>
                        <option value="">Pilih Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-stock="{{ $product->stock }}" 
                                    data-price="{{ $product->price }}">
                                {{ $product->name }} - {{ $product->brand }} (Stok: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                    <input type="number" 
                           name="products[${index}][jumlah]" 
                           min="1" 
                           required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm quantity-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Harga</label>
                    <input type="number" 
                           name="products[${index}][harga]" 
                           min="0" 
                           required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm price-input">
                </div>
            </div>
            <button type="button" 
                    class="mt-2 text-red-600 hover:text-red-800 remove-product">
                Hapus Produk
            </button>
        </div>
    `;
}

document.getElementById('add-product').addEventListener('click', function() {
    productCount++;
    const container = document.getElementById('product-container');
    
    // Create new product entry
    const wrapper = document.createElement('div');
    wrapper.innerHTML = createProductEntry(productCount);
    container.appendChild(wrapper.firstElementChild);
    
    // Add event listeners for the new entry
    const newEntry = container.lastElementChild;
    const select = newEntry.querySelector('select');
    const quantityInput = newEntry.querySelector('.quantity-input');
    const removeButton = newEntry.querySelector('.remove-product');
    
    // Event listener for quantity check
    quantityInput.addEventListener('input', function() {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption.value) {
            checkStock(this, selectedOption.dataset.stock);
        }
    });
    
    // Event listener for remove button
    removeButton.addEventListener('click', function() {
        newEntry.remove();
    });
});

// Add event listener for first product entry
const firstSelect = document.querySelector('select[name="products[0][product_id]"]');
const firstQuantityInput = document.querySelector('input[name="products[0][jumlah]"]');

firstQuantityInput.addEventListener('input', function() {
    const selectedOption = firstSelect.options[firstSelect.selectedIndex];
    if (selectedOption.value) {
        checkStock(this, selectedOption.dataset.stock);
    }
});
</script>
@endpush
@endsection