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
                <div class="product-entry mb-4 bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                            <div class="relative">
                                <input type="text" 
                                       class="search-product mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 rounded-md"
                                       placeholder="Cari produk...">
                                <input type="hidden" name="products[0][product_id]" class="product-id-input" required>
                                <div class="search-results absolute z-10 w-full bg-white mt-1 rounded-md shadow-lg hidden">
                                </div>
                            </div>
                            <p class="selected-product-info mt-2 text-sm text-gray-500"></p>
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

            <!-- Remaining form fields... -->
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
const products = @json($products);
let productCount = 0;

function setupProductSearch(container) {
    const searchInput = container.querySelector('.search-product');
    const searchResults = container.querySelector('.search-results');
    const productIdInput = container.querySelector('.product-id-input');
    const productInfo = container.querySelector('.selected-product-info');
    const quantityInput = container.querySelector('.quantity-input');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        if (searchTerm.length < 1) {
            searchResults.classList.add('hidden');
            return;
        }

        const filteredProducts = products.filter(product => 
            product.name.toLowerCase().includes(searchTerm) || 
            product.brand.toLowerCase().includes(searchTerm)
        );

        searchResults.innerHTML = '';
        filteredProducts.forEach(product => {
            const div = document.createElement('div');
            div.className = 'p-2 hover:bg-gray-100 cursor-pointer';
            div.textContent = `${product.name} - ${product.brand} (Stok: ${product.stock})`;
            div.addEventListener('click', () => {
                productIdInput.value = product.id;
                searchInput.value = `${product.name} - ${product.brand}`;
                productInfo.textContent = `Stok tersedia: ${product.stock}`;
                searchResults.classList.add('hidden');
                
                // Reset quantity input max value
                quantityInput.max = product.stock;
            });
            searchResults.appendChild(div);
        });

        searchResults.classList.remove('hidden');
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
}

function createProductEntry(index) {
    return `
        <div class="product-entry mb-4 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                    <div class="relative">
                        <input type="text" 
                               class="search-product mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 rounded-md"
                               placeholder="Cari produk...">
                        <input type="hidden" name="products[${index}][product_id]" class="product-id-input" required>
                        <div class="search-results absolute z-10 w-full bg-white mt-1 rounded-md shadow-lg hidden">
                        </div>
                    </div>
                    <p class="selected-product-info mt-2 text-sm text-gray-500"></p>
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

// Setup the first product entry
setupProductSearch(document.querySelector('.product-entry'));

document.getElementById('add-product').addEventListener('click', function() {
    productCount++;
    const container = document.getElementById('product-container');
    
    const wrapper = document.createElement('div');
    wrapper.innerHTML = createProductEntry(productCount);
    container.appendChild(wrapper.firstElementChild);
    
    const newEntry = container.lastElementChild;
    setupProductSearch(newEntry);
    
    const removeButton = newEntry.querySelector('.remove-product');
    removeButton.addEventListener('click', function() {
        newEntry.remove();
    });
});

// Function to check stock
function checkStock(input, stock) {
    if (parseInt(input.value) > parseInt(stock)) {
        alert('Jumlah melebihi stok yang tersedia!');
        input.value = stock;
    }
}
</script>
@endpush
@endsection