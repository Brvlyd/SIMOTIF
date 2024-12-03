<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMOTIF - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <!-- Logo -->
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold flex items-center">
                            SIMOTIF
                        </a>
                    </div>
                    
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <!-- Menu Dashboard -->
                        <a href="{{ route('dashboard') }}" class="hover:border-b-2 hover:border-indigo-500 px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'border-b-2 border-indigo-500' : 'text-gray-500' }}">
                            Dashboard
                        </a>
                        <!-- Menu Stock -->
                        <a href="{{ route('stock') }}" class="hover:border-b-2 hover:border-indigo-500 px-3 py-2 text-sm font-medium {{ request()->routeIs('stock') ? 'border-b-2 border-indigo-500' : 'text-gray-500' }}">
                            Stock
                        </a>
                        
                        <!-- Menu untuk Sales Role -->
                        @if(auth()->user()->role === 'sales')
                        <a href="{{ route('products.sold') }}" class="hover:border-b-2 hover:border-indigo-500 px-3 py-2 text-sm font-medium {{ request()->routeIs('products.sold') ? 'border-b-2 border-indigo-500' : 'text-gray-500' }}">
                            Produk Terjual
                        </a>
                        @endif

                        <!-- Menu Input Barang (Hanya untuk Warehouse Role) -->
                        @if(auth()->user()->role === 'warehouse')
                        <a href="{{ route('warehouse.input-barang') }}" class="hover:border-b-2 hover:border-indigo-500 px-3 py-2 text-sm font-medium {{ request()->routeIs('warehouse.input-barang') ? 'border-b-2 border-indigo-500' : 'text-gray-500' }}">
                            Input Barang
                        </a>
                        @endif

                        <!-- Menu Products (Hanya untuk Owner Role) -->
                        @if(auth()->user()->role === 'owner')
                        <a href="{{ route('products.index') }}" class="hover:border-b-2 hover:border-indigo-500 px-3 py-2 text-sm font-medium {{ request()->routeIs('products.*') && !request()->routeIs('products.sold') ? 'border-b-2 border-indigo-500' : 'text-gray-500' }}">
                            Products
                        </a>
                        @endif
                    </div>
                </div>

                <!-- User Info & Logout -->
                <div class="flex items-center">
                    <div class="mr-4 text-sm text-gray-500">
                        {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        @yield('content')
    </main>
</body>
</html>