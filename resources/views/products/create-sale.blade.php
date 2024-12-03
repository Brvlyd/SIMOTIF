@extends('layouts.app')

@section('title', 'Input Penjualan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Input Penjualan Produk</h1>
            <p class="text-sm text-gray-600 mt-1">*Harga akan otomatis terisi dengan margin 30%</p>
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
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                            <div class="relative">
                                <input type="text" 
                                       class="search-product mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 rounded-md"
                                       placeholder="Cari produk...">
                                <input type="hidden" name="products[0][product_id]" class="product-id-input" required>
                                <input type="hidden" name="products[0][harga]" class="price-input">
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
                    <button type="button"
                            id="printReceipt"
                            class=" text-white px-4 py-2 rounded-lg"
                            disabled>
                        
                    </button>
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

        <!-- Receipt Preview -->
        <div id="receiptPreview" class="mt-8 p-4 bg-gray-50 rounded-lg max-w-md mx-auto">
            <div class="text-center mb-4">
                <h2 class="text-xl font-bold">Preview Struk</h2>
                <p class="text-sm text-gray-600" id="receiptDate"></p>
            </div>
            <div id="receiptItems" class="space-y-2">
                <!-- Items will be populated here -->
            </div>
            <div class="border-t border-gray-300 mt-4 pt-4">
                <div class="flex justify-between font-bold">
                    <span>Total:</span>
                    <span id="receiptTotal">Rp 0</span>
                </div>
            </div>
        </div>
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
    const priceInput = container.querySelector('.price-input');

    quantityInput.addEventListener('input', function() {
        const selectedProduct = products.find(p => p.id == productIdInput.value);
        if (selectedProduct) {
            const priceWithMargin = selectedProduct.price * 1.3;
            const totalPrice = priceWithMargin * this.value;
            productInfo.textContent = `Stok tersedia: ${selectedProduct.stock} | Harga: Rp ${formatNumber(totalPrice)}`;
            priceInput.value = totalPrice;
            
            if (parseInt(this.value) > selectedProduct.stock) {
                alert('Jumlah melebihi stok yang tersedia!');
                this.value = selectedProduct.stock;
                const updatedTotal = priceWithMargin * this.value;
                productInfo.textContent = `Stok tersedia: ${selectedProduct.stock} | Harga: Rp ${formatNumber(updatedTotal)}`;
                priceInput.value = updatedTotal;
            }
            updateReceiptPreview();
        }
    });

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
                quantityInput.max = product.stock;
                quantityInput.value = '1';

                const priceWithMargin = product.price * 1.3;
                const totalPrice = priceWithMargin * quantityInput.value;
                productInfo.textContent = `Stok tersedia: ${product.stock} | Harga: Rp ${formatNumber(totalPrice)}`;
                priceInput.value = totalPrice;
                
                searchResults.classList.add('hidden');
                updateReceiptPreview();
            });
            searchResults.appendChild(div);
        });

        searchResults.classList.remove('hidden');
    });

    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
}

function formatNumber(num) {
    return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function createProductEntry(index) {
    return `
        <div class="product-entry mb-4 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                    <div class="relative">
                        <input type="text" 
                               class="search-product mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 rounded-md"
                               placeholder="Cari produk...">
                        <input type="hidden" name="products[${index}][product_id]" class="product-id-input" required>
                        <input type="hidden" name="products[${index}][harga]" class="price-input">
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
            </div>
            <button type="button" 
                    class="mt-2 text-red-600 hover:text-red-800 remove-product">
                Hapus Produk
            </button>
        </div>
    `;
}

function updateReceiptPreview() {
    const receipt = document.getElementById('receiptPreview');
    const receiptItems = document.getElementById('receiptItems');
    const receiptTotal = document.getElementById('receiptTotal');
    const receiptDate = document.getElementById('receiptDate');
    let total = 0;

    // Update date
    const dateInput = document.getElementById('tanggal_jual');
    receiptDate.textContent = new Date(dateInput.value).toLocaleDateString('id-ID');

    // Clear previous items
    receiptItems.innerHTML = '';

    // Add all product entries
    document.querySelectorAll('.product-entry').forEach(entry => {
        const productName = entry.querySelector('.search-product').value;
        const quantity = entry.querySelector('.quantity-input').value;
        const price = entry.querySelector('.price-input').value;

        if (productName && quantity && price) {
            const itemTotal = parseInt(price);
            total += itemTotal;

            const itemDiv = document.createElement('div');
            itemDiv.className = 'flex justify-between text-sm';
            itemDiv.innerHTML = `
                <span>${productName} x ${quantity}</span>
                <span>Rp ${formatNumber(itemTotal)}</span>
            `;
            receiptItems.appendChild(itemDiv);
        }
    });

    receiptTotal.textContent = `Rp ${formatNumber(total)}`;
}

function validateForm() {
    const productEntries = document.querySelectorAll('.product-entry');
    let isValid = true;
    
    productEntries.forEach(entry => {
        const productId = entry.querySelector('.product-id-input').value;
        const quantity = entry.querySelector('.quantity-input').value;
        
        if (!productId || !quantity) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        alert('Harap isi semua data produk dengan lengkap');
        return false;
    }
    
    return true;
}

function setupForm() {
    const form = document.querySelector('form');
    const printButton = document.getElementById('printReceipt');
    printButton.disabled = false;

    async function submitForm(formData, shouldPrint = false) {
        try {
            const response = await fetch('{{ route("products.store-sale") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                if (shouldPrint) {
                    window.location.href = `{{ url('products/print-receipt') }}/${data.sale_id}`;
                    setTimeout(() => {
                        window.location.href = '{{ route("products.sold") }}';
                    }, 1000);
                } else {
                    alert('Penjualan berhasil disimpan!');
                    window.location.href = '{{ route("products.sold") }}';
                }
            } else {
                alert(data.message || 'Terjadi kesalahan saat menyimpan penjualan');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan penjualan');
        }
    }

    printButton.addEventListener('click', async function(e) {
        e.preventDefault();
        if (!validateForm()) return;
        
        const formData = new FormData(form);
        await submitForm(formData, true);
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!validateForm()) return;
        
        const formData = new FormData(this);
        await submitForm(formData, false);
    });
}

// Initial setup
setupProductSearch(document.querySelector('.product-entry'));
setupForm();

// Add event listener for date change
document.getElementById('tanggal_jual').addEventListener('change', updateReceiptPreview);

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
        updateReceiptPreview();
    });
});

// Initial receipt preview
updateReceiptPreview();
</script>
@endpush
@endsection