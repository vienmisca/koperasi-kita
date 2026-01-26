<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Koperasi SMK') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased bg-white text-gray-900">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-gray-900 tracking-tight">KOPERASI</span>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2 text-sm font-medium text-white transition-all bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm hover:shadow">
                                Masuk
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-indigo-50/50 to-white -z-10"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight mb-6">
                Sistem Informasi <br class="hidden sm:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600">Koperasi Sekolah</span>
            </h1>
            <p class="mt-4 text-xl text-gray-500 max-w-2xl mx-auto mb-10">
                Solusi digital terintegrasi untuk pengelolaan transaksi, stok barang, dan pelaporan keuangan koperasi modern.
            </p>
            
            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-3.5 text-base font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-200 transform hover:-translate-y-0.5">
                    Masuk ke Sistem
                    <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-base font-semibold text-indigo-600 tracking-wide uppercase">Fitur Unggulan</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Dikelola dengan Lebih Mudah</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1: POS -->
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 hover:border-indigo-100 transition-colors group">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm text-indigo-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Point of Sales</h3>
                    <p class="text-gray-500 leading-relaxed">
                        Transaksi penjualan kasir yang cepat dan responsif dengan dukungan pemindaian barcode dan kalkulasi otomatis.
                    </p>
                </div>

                <!-- Feature 2: Stock Management -->
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 hover:border-indigo-100 transition-colors group">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm text-indigo-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Manajemen Stok</h3>
                    <p class="text-gray-500 leading-relaxed">
                        Pantau ketersediaan barang secara real-time, kelola inventaris masuk dan keluar dengan pencatatan yang akurat.
                    </p>
                </div>

                <!-- Feature 3: Reports -->
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 hover:border-indigo-100 transition-colors group">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm text-indigo-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Laporan Keuangan</h3>
                    <p class="text-gray-500 leading-relaxed">
                        Rekapitulasi data transaksi harian, bulanan, dan tahunan yang transparan untuk memudahkan audit keuangan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center">
            <div class="flex items-center gap-2 mb-4 opacity-50 grayscale hover:grayscale-0 transition-all">
                <div class="w-6 h-6 bg-indigo-600 rounded flex items-center justify-center text-white text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="font-bold text-lg text-gray-900">KOPERASI</span>
            </div>
            <p class="text-gray-400 text-sm">
                &copy; {{ date('Y') }} Sistem Informasi Koperasi. All rights reserved.
            </p>
        </div>
    </footer>

</body>
</html>
