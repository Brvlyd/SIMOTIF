<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SAHABAT MOTOR - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @stack('styles')
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Desktop Navigation -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600 hover:text-indigo-700 transition-colors duration-200">
                            SAHABAT MOTOR
                        </a>
                    </div>
                    
                    <!-- Desktop Navigation Menu -->
                    <div class="hidden md:ml-8 md:flex md:space-x-6">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-indigo-600 transition-colors duration-200 
                           {{ request()->routeIs('dashboard') ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('stock') }}" 
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-indigo-600 transition-colors duration-200
                           {{ request()->routeIs('stock') ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500' }}">
                            Stock
                        </a>
                        
                        @if(auth()->user()->role === 'sales' || auth()->user()->role === 'owner')
                        <a href="{{ route('products.sold') }}" 
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-indigo-600 transition-colors duration-200
                           {{ request()->routeIs('products.sold') ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500' }}">
                            Produk Terjual
                        </a>
                        @endif

                        @if(auth()->user()->role === 'warehouse' || auth()->user()->role === 'owner')
                        <a href="{{ route('warehouse.input-barang') }}" 
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-indigo-600 transition-colors duration-200
                           {{ request()->routeIs('warehouse.input-barang') ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500' }}">
                            Produk Masuk
                        </a>
                        @endif
                    </div>
                </div>

                <!-- User Menu and Mobile Menu Button -->
                <div class="flex items-center">
                    <!-- User Info -->
                    <div class="hidden md:flex md:items-center">
                        <div class="mr-4 text-sm text-gray-500">
                            {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                                Logout
                            </button>
                        </form>
                    </div>

                    <!-- Mobile menu button -->
                    <button type="button" 
                            onclick="toggleMobileMenu()"
                            class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="hidden md:hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('dashboard') }}" 
                   class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-500 hover:bg-gray-50' }}">
                    Dashboard
                </a>
                <a href="{{ route('stock') }}" 
                   class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('stock') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-500 hover:bg-gray-50' }}">
                    Stock
                </a>
                
                @if(auth()->user()->role === 'sales' || auth()->user()->role === 'owner')
                <a href="{{ route('products.sold') }}" 
                   class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('products.sold') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-500 hover:bg-gray-50' }}">
                    Produk Terjual
                </a>
                @endif

                @if(auth()->user()->role === 'warehouse' || auth()->user()->role === 'owner')
                <a href="{{ route('warehouse.input-barang') }}" 
                   class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('warehouse.input-barang') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-500 hover:bg-gray-50' }}">
                    Produk Masuk
                </a>
                @endif

                <!-- Mobile User Info -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="px-4 text-sm text-gray-500">
                        {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3 px-4">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-indigo-600">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // CSRF Token Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
            }
        }
    </script>

    @stack('scripts')
</body>
</html>