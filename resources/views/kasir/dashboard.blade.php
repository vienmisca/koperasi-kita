@extends('layouts.app')

@section('content')
<div class="space-y-6 animate-fade-in-down">
    
    <!-- Hero Section / Welcome -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold">Halo, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-indigo-100 mt-1">Siap untuk melayani pelanggan hari ini?</p>
            </div>
            <a href="{{ route('transaksi') }}" class="bg-white text-indigo-600 px-6 py-2.5 rounded-xl font-bold shadow-md hover:bg-gray-50 transition-transform active:scale-95 flex items-center gap-2">
                <span>🛒</span> Buat Transaksi Baru
            </a>
        </div>
        
        <!-- Decorative Circles -->
        <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white opacity-10 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 bg-white opacity-10 rounded-full"></div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 hover:border-indigo-200 transition-colors group">
            <div class="flex justify-between items-start">
                <div>
                     <p class="text-sm text-gray-500 font-medium">Transaksi Hari Ini</p>
                     <h3 class="text-3xl font-bold text-gray-800 mt-1">24</h3>
                </div>
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <div class="text-xs text-green-600 font-medium flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span>+12% dari kemarin</span>
            </div>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 hover:border-green-200 transition-colors group">
             <div class="flex justify-between items-start">
                <div>
                     <p class="text-sm text-gray-500 font-medium">Pendapatan Hari Ini</p>
                     <h3 class="text-3xl font-bold text-gray-800 mt-1">Rp 1.2jt</h3>
                </div>
                <div class="w-10 h-10 bg-green-50 text-green-600 rounded-lg flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-colors">
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
             <div class="text-xs text-green-600 font-medium flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span>On target</span>
            </div>
        </div>

        <!-- Card 3 (Low Stock) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 hover:border-red-200 transition-colors group">
             <div class="flex justify-between items-start">
                <div>
                     <p class="text-sm text-gray-500 font-medium">Stok Menipis</p>
                     <h3 class="text-3xl font-bold text-gray-800 mt-1">3 <span class="text-sm text-gray-400 font-normal">item</span></h3>
                </div>
                <div class="w-10 h-10 bg-red-50 text-red-600 rounded-lg flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-colors">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
             <div class="text-xs text-red-500 font-medium hover:underline cursor-pointer">
                Lihat detail &rarr;
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table (Expanded) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex-1 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800">Transaksi Terakhir</h3>
            <a href="{{ route('kasir.laporan') }}" class="text-sm text-indigo-600 font-medium hover:text-indigo-700">Lihat Semua</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-medium">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-lg">ID Transaksi</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Item</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4 rounded-tr-lg">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <!-- Placeholder Rows -->
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-4 font-mono text-sm text-indigo-600 font-medium bg-white">
                            #TRX-001
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">10:42 AM</td>
                        <td class="px-6 py-4 text-sm text-gray-800">Kopi Kapal Api x2, Gula...</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">Rp 45.000</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold border border-green-100">
                                Lunas
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-sm text-indigo-600 font-medium bg-white">
                            #TRX-002
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">10:15 AM</td>
                        <td class="px-6 py-4 text-sm text-gray-800">Indomie Goreng x5</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">Rp 15.000</td>
                         <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold border border-green-100">
                                Lunas
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-sm text-indigo-600 font-medium bg-white">
                            #TRX-003
                        </td>
                         <td class="px-6 py-4 text-sm text-gray-500">09:55 AM</td>
                        <td class="px-6 py-4 text-sm text-gray-800">Roti Tawar, Selai...</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">Rp 32.500</td>
                         <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold border border-green-100">
                                Lunas
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
