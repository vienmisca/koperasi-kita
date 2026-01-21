@extends('layouts.admin')

@section('content')
<div class="space-y-6 animate-fade-in-down">
    
    <!-- Welcome Header -->
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
            <p class="text-gray-500 mt-1">Selamat datang kembali, {{ Auth::user()->name }}! Berikut ringkasan hari ini.</p>
        </div>
        <div class="hidden md:block">
            <span class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg font-medium text-sm">
                📅 {{ date('d F Y') }}
            </span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Stok Barang</p>
                <h3 class="text-2xl font-bold text-gray-800">1,240</h3>
            </div>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pendapatan Hari Ini</p>
                <h3 class="text-2xl font-bold text-gray-800">Rp 4.500.000</h3>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Transaksi Hari Ini</p>
                <h3 class="text-2xl font-bold text-gray-800">45</h3>
            </div>
        </div>

         <!-- Card 4 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total User Aktif</p>
                <h3 class="text-2xl font-bold text-gray-800">8</h3>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Recent Transactions -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-gray-800">Aktivitas Terbaru</h3>
                <button class="text-sm text-indigo-600 font-medium hover:text-indigo-700">Lihat Semua</button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-400 uppercase border-b border-gray-50">
                            <th class="pb-3 pl-2 font-medium">User</th>
                            <th class="pb-3 font-medium">Aktivitas</th>
                            <th class="pb-3 font-medium">Waktu</th>
                            <th class="pb-3 font-medium text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <!-- Dummy Data -->
                         <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                            <td class="py-4 pl-2 font-medium text-gray-800">Kasir 1</td>
                            <td class="py-4 text-gray-500">Menambahkan Transaksi #TRX-982</td>
                            <td class="py-4 text-gray-400 text-xs">2 menit lalu</td>
                            <td class="py-4 text-right"><span class="px-2.5 py-1 bg-green-50 text-green-600 rounded-full text-xs font-medium">Sukses</span></td>
                        </tr>
                        <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                            <td class="py-4 pl-2 font-medium text-gray-800">Admin</td>
                            <td class="py-4 text-gray-500">Update Stok Barang: Kopi Kapal Api</td>
                            <td class="py-4 text-gray-400 text-xs">15 menit lalu</td>
                            <td class="py-4 text-right"><span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-medium">Updated</span></td>
                        </tr>
                        <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                            <td class="py-4 pl-2 font-medium text-gray-800">Kasir 2</td>
                            <td class="py-4 text-gray-500">Login ke sistem</td>
                            <td class="py-4 text-gray-400 text-xs">1 jam lalu</td>
                            <td class="py-4 text-right"><span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Login</span></td>
                        </tr>
                     </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Low Stock Alerts -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
             <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-gray-800">Peringatan Stok</h3>
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center gap-3 p-3 bg-red-50 rounded-xl border border-red-100">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-red-500 shadow-sm font-bold">!</div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Indomie Goreng</h4>
                        <p class="text-xs text-red-500 font-medium">Sisa Stok: 5 pcs</p>
                    </div>
                </div>

                 <div class="flex items-center gap-3 p-3 bg-yellow-50 rounded-xl border border-yellow-100">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-yellow-500 shadow-sm font-bold">!</div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Gula Pasir 1kg</h4>
                        <p class="text-xs text-yellow-600 font-medium">Sisa Stok: 12 pcs</p>
                    </div>
                </div>
                
                 <div class="text-center mt-4">
                     <a href="{{ route('stock.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua stok &rarr;</a>
                 </div>
            </div>
        </div>
    </div>
</div>
@endsection