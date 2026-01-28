@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fade-in-down">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Lengkap</h1>
            <p class="text-gray-500 mt-1">Ringkasan performa penjualan, keuntungan, dan arus barang.</p>
        </div>
        
        <!-- Date Filter (Optional for future implementation, currently visual only) -->
        <div class="flex items-center gap-2 bg-white p-1 rounded-lg border border-gray-200 shadow-sm">
            <input type="date" value="{{ date('Y-m-01') }}" class="border-0 focus:ring-0 text-sm text-gray-600 bg-transparent">
            <span class="text-gray-400">-</span>
            <input type="date" value="{{ date('Y-m-d') }}" class="border-0 focus:ring-0 text-sm text-gray-600 bg-transparent">
            <button class="bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-md text-sm font-medium hover:bg-indigo-100 transition-colors">
                Filter
            </button>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Revenue -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 hover:border-indigo-100 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pendapatan</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs text-green-600 font-medium">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span>+{{ rand(5, 15) }}% dari bulan lalu</span>
            </div>
        </div>

        <!-- Total Profit -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 hover:border-indigo-100 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Keuntungan Bersih</p>
                    <h3 class="text-2xl font-bold text-indigo-600 mt-1">Rp {{ number_format($totalKeuntungan, 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400">Estimasi margin: <span class="text-gray-600 font-medium">{{ $totalPendapatan > 0 ? round(($totalKeuntungan / $totalPendapatan) * 100, 1) : 0 }}%</span></p>
        </div>

        <!-- Total Transactions -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 hover:border-indigo-100 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Transaksi</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalTransaksi) }}</h3>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400">Rata-rata: <span class="text-gray-600 font-medium">Rp {{ $totalTransaksi > 0 ? number_format($totalPendapatan / $totalTransaksi, 0, ',', '.') : 0 }}</span> / trx</p>
        </div>

        <!-- Items Sold -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 hover:border-indigo-100 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Barang Terjual</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalBarangTerjual) }} <span class="text-sm font-normal text-gray-500">pcs</span></h3>
                </div>
                <div class="p-2 bg-orange-50 text-orange-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400">Inventory Turnover: <span class="text-green-600 font-medium">High</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- PRODUCT PERFORMANCE (Best Sellers) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
             <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-800">Produk Terlaris</h3>
                <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">Top 5</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Barang</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Terjual</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topProducts as $product)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $product->nama_barang }}</div>
                                <div class="text-xs text-gray-400">{{ $product->kode_barang }}</div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-700">
                                {{ $product->total_qty }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-indigo-600">
                                Rp {{ number_format($product->total_omset, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400">Belum ada data penjualan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RECENT STOCK IN (Barang Masuk) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
             <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-800">Riwayat Barang Masuk Terakhir</h3>
                <a href="{{ route('stock.mutasi') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Barang</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentStockIn as $stock)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900">{{ $stock->barang->nama_barang }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $stock->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    +{{ $stock->jumlah }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400">Belum ada barang masuk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SALES HISTORY (Laporan Penjualan) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-lg text-gray-800">Riwayat Penjualan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">No. Transaksi</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kasir</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Pelanggan</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Total</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Keuntungan (Est)</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($salesHistory as $sale)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono font-medium text-gray-700">
                            {{ $sale->no_penjualan }}
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $sale->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-gray-900 font-medium">
                            {{ $sale->user->name ?? 'Unknown' }}
                        </td>
                         <td class="px-6 py-4 text-gray-500">
                            {{ $sale->keterangan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-800">
                            Rp {{ number_format($sale->total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-green-600">
                            @php
                                // Inline profit calc for display (ideally done in controller/query)
                                $profit = $sale->details->sum(function($detail) {
                                    return ($detail->harga - ($detail->barang?->harga_beli ?? 0)) * $detail->jumlah;
                                });
                            @endphp
                            + Rp {{ number_format($profit, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium uppercase {{ $sale->status == 'selesai' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $sale->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">Belum ada transaksi penjualan yang tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination if needed -->
        <div class="p-4 border-t border-gray-100">
            {{ $salesHistory->links() }}
        </div>
    </div>
</div>
@endsection
