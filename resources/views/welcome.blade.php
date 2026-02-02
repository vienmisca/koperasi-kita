<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Koperasi Kita') }} - Solusi Koperasi Modern</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        
        /* Custom Animations */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }

        @keyframes success-pop {
            0% { opacity: 0; transform: scale(0.8) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-success-pop { animation: success-pop 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        @keyframes checkmark {
            0% { stroke-dashoffset: 100; opacity: 0; }
            100% { stroke-dashoffset: 0; opacity: 1; }
        }
        .animate-checkmark { stroke-dasharray: 100; animation: checkmark 1s ease-in-out forwards 0.3s; opacity: 0; }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900 selection:bg-indigo-100 selection:text-indigo-700 overflow-x-hidden">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-violet-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-gray-900 tracking-tight">KOPERASI KITA</span>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-100 hover:text-indigo-600 transition-all">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="group relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-white transition-all duration-200 bg-gray-900 font-pj rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 hover:bg-gray-800 shadow-lg hover:shadow-xl">
                                Masuk Aplikasi
                                <div class="absolute -inset-3 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 opacity-20 blur transition duration-1000 group-hover:opacity-40 group-hover:duration-200"></div>
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Decor -->
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-50/80 via-white to-gray-50"></div>
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-indigo-200 rounded-full blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-violet-200 rounded-full blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-pink-100 rounded-full blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-white border border-indigo-100 shadow-sm text-indigo-600 text-sm font-semibold tracking-wide gap-2 mb-8 animate-fade-in-down">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                Sistem Manajemen Koperasi Terpadu
            </div>
            
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-gray-900 tracking-tight mb-8 leading-tight animate-fade-in-down" style="animation-delay: 0.1s;">
                Koperasi Modern <br class="hidden sm:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600">Mudah & Terpercaya</span>
            </h1>
            
            <p class="mt-4 text-xl text-gray-500 max-w-2xl mx-auto mb-12 leading-relaxed animate-fade-in-down" style="animation-delay: 0.2s;">
                Platform digital all-in-one untuk mengelola transaksi penjualan, memantau stok barang, dan laporan keuangan koperasi sekolah dengan real-time.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fade-in-down" style="animation-delay: 0.3s;">
                <a href="#features" class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white bg-indigo-600 rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200 transform hover:-translate-y-1">
                    Lihat Fitur
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-gray-700 bg-white border border-gray-200 rounded-2xl hover:bg-gray-50 hover:text-indigo-600 transition-all shadow-sm hover:shadow-md">
                    Login Sistem
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section with Alternating Layout -->
    <div id="features" class="py-24 overflow-hidden space-y-24">
        
        <!-- Feature 1: POS Transaction (Text Left, Image Right) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                <div class="lg:w-1/2 space-y-6">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold tracking-wide uppercase border border-indigo-100">
                        ⚡ Point of Sales
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Transaksi Kasir Cepat & Akurat</h2>
                    <p class="text-lg text-gray-500 leading-relaxed">
                        Antarmuka kasir yang dirancang khusus untuk kecepatan pelayanan di jam istirahat. Scan barcode, hitung otomatis, dan cetak struk dalam hitungan detik.
                    </p>
                    <ul class="space-y-3 mt-4">
                        <li class="flex items-center gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Scan Barcode & Pencarian Cepat
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Kalkulasi Total Otomatis
                        </li>
                    </ul>
                </div>
                
                <!-- Mini POS UI (Frameless) -->
                <div class="lg:w-1/2 relative">
                    <div class="absolute -inset-4 bg-gradient-to-r from-indigo-100 to-violet-100 rounded-[2rem] transform rotate-2 opacity-50"></div>
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 relative z-10 p-2">
                        <div class="bg-gray-50 rounded-xl overflow-hidden h-[320px] flex text-[10px] sm:text-xs select-none pointer-events-none">
                            <!-- Left: Grid Items -->
                            <div class="w-2/3 p-3 border-r border-gray-200 bg-white">
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="bg-white border rounded-lg p-2 text-center shadow-sm"><div class="text-xl mb-1">📚</div><div class="font-bold text-gray-800">Buku</div></div>
                                    <div class="bg-white border rounded-lg p-2 text-center shadow-sm"><div class="text-xl mb-1">✏️</div><div class="font-bold text-gray-800">Pensil</div></div>
                                    <div class="bg-white border rounded-lg p-2 text-center shadow-sm"><div class="text-xl mb-1">📏</div><div class="font-bold text-gray-800">Penggaris</div></div>
                                    <div class="bg-white border rounded-lg p-2 text-center shadow-sm"><div class="text-xl mb-1">✂️</div><div class="font-bold text-gray-800">Gunting</div></div>
                                </div>
                            </div>
                            <!-- Right: Cart -->
                            <div class="w-1/3 flex flex-col bg-gray-50">
                                <div class="p-3 flex-1 space-y-2">
                                    <div class="bg-white p-2 rounded border shadow-sm flex justify-between"><span>Buku</span><span class="font-bold">10k</span></div>
                                    <div class="bg-white p-2 rounded border shadow-sm flex justify-between"><span>Pensil</span><span class="font-bold">3k</span></div>
                                </div>
                                <div class="p-3 bg-white border-t">
                                    <div class="flex justify-between font-bold text-indigo-600 mb-2"><span>Total</span><span>13k</span></div>
                                    <div class="bg-indigo-600 text-white text-center py-1.5 rounded-lg">Bayar</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Success Overlay (Static for visual) -->
                        <div class="absolute inset-0 flex items-center justify-center bg-black/5 backdrop-blur-[1px]">
                            <div class="bg-white p-4 rounded-xl shadow-lg flex items-center gap-3 animate-success-pop">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 text-sm">Berhasil!</div>
                                    <div class="text-xs text-gray-500">Struk tercetak</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature 2: Stock Management (Image Left, Text Right) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row-reverse items-center gap-12 lg:gap-20">
                <div class="lg:w-1/2 space-y-6">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold tracking-wide uppercase border border-blue-100">
                        📦 Inventory System
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Kontrol Stok Real-time</h2>
                    <p class="text-lg text-gray-500 leading-relaxed">
                        Tidak ada lagi selisih stok. Pantau persediaan barang keluar-masuk secara otomatis. Dapatkan notifikasi saat stok menipis.
                    </p>
                    <ul class="space-y-3 mt-4">
                        <li class="flex items-center gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Manajemen Barang Masuk & Keluar
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Stock Opname Mudah
                        </li>
                    </ul>
                </div>
                
                <!-- Mini Stock UI -->
                <div class="lg:w-1/2 relative">
                    <div class="absolute -inset-4 bg-gradient-to-l from-blue-100 to-cyan-100 rounded-[2rem] transform -rotate-2 opacity-50"></div>
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 relative z-10 p-6 flex flex-col gap-4">
                        <!-- Header Mockup -->
                        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
                            <div class="text-sm font-bold text-gray-700">Stock Dashboard</div>
                            <div class="flex gap-2">
                                <div class="w-20 h-2 bg-gray-100 rounded"></div>
                            </div>
                        </div>
                        <!-- Stock Items -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-blue-50 transition-colors cursor-default">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm text-lg">👕</div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-800">Seragam Batik</div>
                                        <div class="text-xs text-gray-500">Size L</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-blue-600">45 Pcs</div>
                                    <div class="text-[10px] text-green-500 flex items-center gap-1 justify-end">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                        In Stock
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-blue-50 transition-colors cursor-default">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm text-lg">🧢</div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-800">Topi Sekolah</div>
                                        <div class="text-xs text-gray-500">All Size</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-orange-500">5 Pcs</div>
                                    <div class="text-[10px] text-orange-500 flex items-center gap-1 justify-end">
                                        ⚠️ Low Stock
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-blue-50 transition-colors cursor-default">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm text-lg">📚</div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-800">Buku Gambar</div>
                                        <div class="text-xs text-gray-500">A4</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-blue-600">120 Pcs</div>
                                    <div class="text-[10px] text-green-500 flex items-center gap-1 justify-end">
                                        Stable
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature 3: Reports (Text Left, Image Right) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                <div class="lg:w-1/2 space-y-6">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-rose-50 text-rose-600 text-xs font-semibold tracking-wide uppercase border border-rose-100">
                        📈 Analytics
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Laporan Keuangan Transparan</h2>
                    <p class="text-lg text-gray-500 leading-relaxed">
                        Data penjualan terekam otomatis dan disajikan dalam bentuk grafik yang mudah dipahami. Pantau omset harian hingga laba bulanan.
                    </p>
                    <ul class="space-y-3 mt-4">
                        <li class="flex items-center gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Export Laporan Excel
                        </li>
                        <li class="flex items-center gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Grafik Penjualan Bulanan
                        </li>
                    </ul>
                </div>
                
                <!-- Mini Chart UI -->
                <div class="lg:w-1/2 relative">
                    <div class="absolute -inset-4 bg-gradient-to-r from-rose-100 to-orange-100 rounded-[2rem] transform rotate-2 opacity-50"></div>
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 relative z-10 p-6">
                         <div class="flex justify-between items-end mb-6">
                             <div>
                                 <div class="text-sm text-gray-500 font-medium">Total Pendapatan (Bulan Ini)</div>
                                 <div class="text-3xl font-bold text-gray-900 mt-1">Rp 15.450.000</div>
                                 <div class="text-xs text-green-500 mt-1 font-semibold">⬆ 12% dari bulan lalu</div>
                             </div>
                             <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                             </div>
                         </div>
                         <!-- Fake Chart Bars -->
                         <div class="flex items-end gap-3 h-40 pt-4 border-t border-gray-50 px-2 justify-between">
                             <div class="w-full bg-rose-50 rounded-t-lg relative group h-[40%]"><div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-[10px] text-gray-500 opacity-0 group-hover:opacity-100">1.2jt</div></div>
                             <div class="w-full bg-rose-100 rounded-t-lg relative group h-[60%]"><div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-[10px] text-gray-500 opacity-0 group-hover:opacity-100">2.5jt</div></div>
                             <div class="w-full bg-rose-200 rounded-t-lg relative group h-[30%]"><div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-[10px] text-gray-500 opacity-0 group-hover:opacity-100">800k</div></div>
                             <div class="w-full bg-rose-300 rounded-t-lg relative group h-[75%]"><div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-[10px] text-gray-500 opacity-0 group-hover:opacity-100">3.2jt</div></div>
                             <div class="w-full bg-rose-500 text-white rounded-t-lg relative shadow-lg shadow-rose-200 h-[90%]"><div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-[10px] font-bold text-rose-600">4.1jt</div></div>
                             <div class="w-full bg-rose-200 rounded-t-lg relative group h-[50%]"><div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-[10px] text-gray-500 opacity-0 group-hover:opacity-100">1.8jt</div></div>
                         </div>
                         <div class="flex justify-between mt-2 text-[10px] text-gray-400 font-medium px-2">
                             <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                         </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Final CTA -->
    <div class="py-20 bg-indigo-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-[url('https://grainy-gradients.vercel.app/noise.svg')]"></div>
        <div class="absolute right-0 top-0 -mt-20 -mr-20 w-96 h-96 bg-indigo-500 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute left-0 bottom-0 -mb-20 -ml-20 w-96 h-96 bg-violet-500 rounded-full blur-3xl opacity-50"></div>
        
        <div class="max-w-4xl mx-auto text-center relative z-10 px-4">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Siap untuk Mengelola Koperasi?</h2>
            <p class="text-indigo-200 text-lg mb-10 max-w-2xl mx-auto">
                Tingkatkan efisiensi dan transparansi koperasi sekolah Anda dengan sistem kami yang modern dan mudah digunakan.
            </p>
            <a href="{{ route('login') }}" class="inline-block px-10 py-5 bg-white text-indigo-900 font-bold text-xl rounded-2xl hover:bg-gray-50 hover:scale-105 transition-all shadow-xl">
                Mulai Sekarang Gratis
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-8">
               <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-gray-900">Koperasi Kita</span>
                </div>
                <div class="flex gap-6 text-gray-500">
                    <a href="#" class="hover:text-indigo-600 transition-colors">Tentang</a>
                    <a href="#" class="hover:text-indigo-600 transition-colors">Fitur</a>
                    <a href="#" class="hover:text-indigo-600 transition-colors">Bantuan</a>
                </div>
            </div>
            
            <div class="w-full h-px bg-gray-100 mb-8"></div>

            <div class="text-center text-gray-400 text-sm">
                &copy; {{ date('Y') }} Koperasi Kita. All rights reserved. Made for Education.
            </div>
        </div>
    </footer>

</body>
</html>
