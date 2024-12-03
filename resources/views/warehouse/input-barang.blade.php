@extends('layouts.app')

@section('title', 'Input Barang Baru')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
   <div class="bg-white rounded-lg shadow-lg p-6">
       <div class="flex justify-between items-center mb-6">
           <h1 class="text-2xl font-semibold text-gray-900">Input Produk Masuk</h1>
       </div>

       @if(session('success'))
       <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
           {{ session('success') }}
       </div>
       @endif

       @if(session('error'))
       <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
           {{ session('error') }}
       </div>
       @endif

       <!-- Summary Cards -->
       <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
           <div class="bg-blue-50 p-4 rounded-lg">
               <h3 class="text-lg font-semibold text-blue-700">Total Transaksi</h3>
               <p class="text-2xl font-bold text-blue-800">{{ $totalTransactions }}</p>
           </div>
           <div class="bg-green-50 p-4 rounded-lg">
               <h3 class="text-lg font-semibold text-green-700">Total Produk Masuk</h3>
               <p class="text-2xl font-bold text-green-800">{{ $totalProducts }}</p>
           </div>
           <div class="bg-purple-50 p-4 rounded-lg">
               <h3 class="text-lg font-semibold text-purple-700">Total Jenis Produk</h3>
               <p class="text-2xl font-bold text-purple-800">{{ $uniqueProducts }}</p>
           </div>
       </div>

       <!-- Product Input Form -->
       <div class="bg-white rounded-lg shadow mb-6">
           <div class="p-6">
               <form action="{{ route('warehouse.simpan-barang') }}" method="POST" class="space-y-6">
                   @csrf
                   <div id="product-container">
                       <div class="product-entry bg-gray-50 p-4 rounded-lg mb-4">
                           <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                               <!-- Nama Suku Cadang -->
                               <div>
                                   <label class="block text-sm font-medium text-gray-700">Nama Suku Cadang</label>
                                   <input type="text" 
                                          name="products[0][name]" 
                                          required
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                   @error('products.0.name')
                                       <span class="text-red-500 text-sm">{{ $message }}</span>
                                   @enderror
                               </div>

                               <!-- Merek Suku Cadang -->
                               <div>
                                   <label class="block text-sm font-medium text-gray-700">Merek Suku Cadang</label>
                                   <input type="text" 
                                          name="products[0][brand]" 
                                          required
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                   @error('products.0.brand')
                                       <span class="text-red-500 text-sm">{{ $message }}</span>
                                   @enderror
                               </div>

                               <!-- Tipe Kendaraan -->
                               <div>
                                   <label class="block text-sm font-medium text-gray-700">Tipe Kendaraan</label>
                                   <input type="text" 
                                          name="products[0][vehicle_type]" 
                                          required
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                   @error('products.0.vehicle_type')
                                       <span class="text-red-500 text-sm">{{ $message }}</span>
                                   @enderror
                               </div>

                               <!-- Jumlah -->
                               <div>
                                   <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                                   <input type="number" 
                                          name="products[0][stock]" 
                                          min="1" 
                                          required
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                   @error('products.0.stock')
                                       <span class="text-red-500 text-sm">{{ $message }}</span>
                                   @enderror
                               </div>

                               <!-- Harga -->
                               <div>
                                   <label class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                                   <input type="text" 
                                          name="products[0][price]" 
                                          required
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 price-input"
                                          placeholder="Contoh: 150000">
                                   @error('products.0.price')
                                       <span class="text-red-500 text-sm">{{ $message }}</span>
                                   @enderror
                               </div>
                           </div>
                       </div>
                   </div>

                   <div class="flex justify-between items-center mt-6">
                       <button type="button" 
                               id="add-product"
                               class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                           + Tambah Produk
                       </button>
                       
                       <div class="flex space-x-4">
                           <a href="{{ route('stock') }}" 
                              class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                               Batal
                           </a>
                           <button type="submit" 
                                   class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                               Simpan
                           </button>
                       </div>
                   </div>
               </form>
           </div>
       </div>

       <!-- Transaction History Table -->
       <div class="overflow-x-auto">
           <table class="min-w-full divide-y divide-gray-200">
               <thead class="bg-gray-50">
                   <tr>
                       <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                       <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                       <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                       <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                       <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Masuk</th>
                       <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                       <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stok</th>
                       <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                   </tr>
               </thead>
               <tbody class="bg-white divide-y divide-gray-200">
                   @forelse($transactions as $index => $transaction)
                   <tr>
                       <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                       <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->date->format('d/m/Y') }}</td>
                       <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->product->name }}</td>
                       <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->product->brand }}</td>
                       <td class="px-6 py-4 whitespace-nowrap">
                           <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                               {{ $transaction->quantity }}
                           </span>
                       </td>
                       <td class="px-6 py-4 whitespace-nowrap">
                           Rp {{ number_format($transaction->product->price, 0, ',', '.') }}
                       </td>
                       <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->product->stock }}</td>
                       <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->user->name }}</td>
                   </tr>
                   @empty
                   <tr>
                       <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                           Tidak ada data transaksi
                       </td>
                   </tr>
                   @endforelse
               </tbody>
           </table>
       </div>

       @if($transactions->hasPages())
       <div class="mt-4">
           {{ $transactions->links() }}
       </div>
       @endif
   </div>
</div>

@push('scripts')
<script>
let productCount = 0;

// Fungsi untuk format harga
function formatPrice(input) {
   // Hapus semua karakter non-digit
   let value = input.value.replace(/\D/g, '');
   
   // Format dengan titik sebagai pemisah ribuan
   value = new Intl.NumberFormat('id-ID').format(value);
   
   // Update nilai input
   input.value = value;
}

// Tambahkan event listener untuk input harga yang sudah ada
document.querySelectorAll('.price-input').forEach(input => {
   input.addEventListener('input', function() {
       formatPrice(this);
   });
});

document.getElementById('add-product').addEventListener('click', function() {
   productCount++;
   const container = document.getElementById('product-container');
   
   const productEntry = document.createElement('div');
   productEntry.className = 'product-entry bg-gray-50 p-4 rounded-lg mb-4';
   
   const productHtml = `
       <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
           <div>
               <label class="block text-sm font-medium text-gray-700">Nama Suku Cadang</label>
               <input type="text" 
                      name="products[${productCount}][name]" 
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
           </div>
           <div>
               <label class="block text-sm font-medium text-gray-700">Merek Suku Cadang</label>
               <input type="text" 
                      name="products[${productCount}][brand]" 
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
           </div>
           <div>
               <label class="block text-sm font-medium text-gray-700">Tipe Kendaraan</label>
               <input type="text" 
                      name="products[${productCount}][vehicle_type]" 
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
           </div>
           <div>
               <label class="block text-sm font-medium text-gray-700">Jumlah</label>
               <input type="number" 
                      name="products[${productCount}][stock]" 
                      min="1" 
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
           </div>
           <div>
               <label class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
               <input type="text" 
                      name="products[${productCount}][price]" 
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 price-input"
                      placeholder="Contoh: 150000">
           </div>
       </div>
       <button type="button" 
               class="mt-2 text-red-600 hover:text-red-800 focus:outline-none remove-product">
           Hapus Produk
       </button>
   `;
   
   productEntry.innerHTML = productHtml;
   
   // Add event listener for price formatting on new input
   const priceInput = productEntry.querySelector('.price-input');
   priceInput.addEventListener('input', function() {
       formatPrice(this);
   });
   
   // Add event listener for remove button
   const removeButton = productEntry.querySelector('.remove-product');
   removeButton.addEventListener('click', function() {
       productEntry.remove();
   });
   
   container.appendChild(productEntry);
});

// Form validation before submit
document.querySelector('form').addEventListener('submit', function(e) {
   const inputs = this.querySelectorAll('input[required]');
   let isValid = true;

   inputs.forEach(input => {
       if (!input.value.trim()) {
           isValid = false;
           input.classList.add('border-red-500');
       } else {
           input.classList.remove('border-red-500');
       }
   });

   if (!isValid) {
       e.preventDefault();
       alert('Mohon lengkapi semua field yang diperlukan');
   }
   
   // Prepare price values before submit
   this.querySelectorAll('.price-input').forEach(input => {
       input.value = input.value.replace(/\D/g, '');
   });
});
</script>
@endpush

@endsection