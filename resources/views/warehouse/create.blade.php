<!-- resources/views/warehouse/create.blade.php -->
@extends('layouts.app')

@section('title', 'Input Barang Warehouse')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">Input Barang Warehouse</h2>
    </div>

    <div class="p-6">
        <form action="{{ route('warehouse.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Suku Cadang -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nama Suku Cadang
                    </label>
                    <input type="text" name="name" id="name" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                </div>

                <!-- Merek Suku Cadang -->
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700">
                        Merek Suku Cadang
                    </label>
                    <input type="text" name="brand" id="brand" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                </div>

                <!-- Tipe Kendaraan -->
                <div>
                    <label for="vehicle_type" class="block text-sm font-medium text-gray-700">
                        Tipe Kendaraan
                    </label>
                    <input type="text" name="vehicle_type" id="vehicle_type" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                </div>

                <!-- Jumlah Tersedia -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">
                        Jumlah Tersedia
                    </label>
                    <input type="number" name="stock" id="stock" min="0"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('stock') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </a>
                <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success Message -->
@if (session('success'))
<div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif

<!-- Error Messages -->
@if ($errors->any())
<div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
    <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif