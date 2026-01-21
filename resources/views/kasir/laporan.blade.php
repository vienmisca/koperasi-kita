@extends('layouts.app')

@section('content')
<div class="animate-fade-in-down">
    
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span>📊</span> History Pembelian
            </h1>
            <p class="text-gray-600 mt-1">Rekap data transaksi penjualan.</p>
        </div>
        
        <div class="flex gap-2">
            <button class="px-4 py-2 bg-white border rounded-lg text-gray-600 hover:bg-gray-50 flex items-center gap-2 shadow-sm">
                <span>📅</span> Filter Tanggal
            </button>
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2 shadow shadow-green-200">
                <span>📥</span> Export Excel
            </button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500 font-medium mb-1">Total Pendapatan (Hari Ini)</p>
            <h3 class="text-2xl font-bold text-gray-900">Rp 0</h3>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500 font-medium mb-1">Total Transaksi</p>
            <h3 class="text-2xl font-bold text-gray-900">0</h3>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500 font-medium mb-1">Rata-rata Transaksi</p>
            <h3 class="text-2xl font-bold text-gray-900">Rp 0</h3>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-sm">ID Transaksi</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-sm">Waktu</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-sm">Item</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-sm">Total Belanja</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-sm">Pembayaran</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-sm text-center">Status</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-sm text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Placeholder Data if Empty -->
                    @if(empty($laporan))
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-4xl mb-3 opacity-20">🧾</span>
                                <p>Belum ada data transaksi.</p>
                            </div>
                        </td>
                    </tr>
                    @else
                        <!-- Loop data here later -->
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t flex items-center justify-between">
            <span class="text-sm text-gray-500">Menampilkan 0 dari 0 data</span>
            <div class="flex gap-1">
                <button class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50" disabled>&lt;</button>
                <button class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50" disabled>&gt;</button>
            </div>
        </div>
    </div>
</div>
@endsection
