@extends('layouts.app')

@section('content')
<div class="animate-fade-in-down" x-data="{ filterOpen: false }">
    
    <!-- Header with Filter -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span>📊</span> Laporan Penjualan
            </h1>
            <p class="text-gray-600 mt-1">
                Data dari <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span> 
                s/d <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
            </p>
        </div>
        
        <form method="GET" action="{{ route('kasir.laporan') }}" class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
            <div class="flex items-center gap-2 bg-white px-3 py-2 border rounded-lg shadow-sm">
                <span class="text-gray-400 text-xs uppercase font-bold">Dari</span>
                <input type="date" name="start_date" value="{{ $startDate }}" class="border-0 p-0 text-sm focus:ring-0 text-gray-700">
            </div>
            
            <div class="flex items-center gap-2 bg-white px-3 py-2 border rounded-lg shadow-sm">
                <span class="text-gray-400 text-xs uppercase font-bold">Sampai</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="border-0 p-0 text-sm focus:ring-0 text-gray-700">
            </div>

            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium shadow-lg hover:shadow-indigo-500/30 transition-all">
                Filter
            </button>
            
            <a href="{{ route('kasir.laporan.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium shadow-lg hover:shadow-green-500/30 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                 <svg class="w-20 h-20 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm text-gray-500 font-medium mb-1 uppercase tracking-wide">Total Pendapatan</p>
            <h3 class="text-3xl font-bold text-gray-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
        </div>
        
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                 <svg class="w-20 h-20 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <p class="text-sm text-gray-500 font-medium mb-1 uppercase tracking-wide">Total Transaksi</p>
            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($totalTransaksi) }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
             <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                 <svg class="w-20 h-20 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <p class="text-sm text-gray-500 font-medium mb-1 uppercase tracking-wide">Rata-rata / Transaksi</p>
            <h3 class="text-3xl font-bold text-gray-900">Rp {{ number_format($rataRata, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 font-bold text-gray-600 text-xs uppercase tracking-wider">No Transaksi</th>
                        <th class="px-6 py-4 font-bold text-gray-600 text-xs uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 font-bold text-gray-600 text-xs uppercase tracking-wider">Kasir</th>
                        <th class="px-6 py-4 font-bold text-gray-600 text-xs uppercase tracking-wider">Detail Item</th>
                        <th class="px-6 py-4 font-bold text-gray-600 text-xs uppercase tracking-wider text-right">Total</th>
                        <th class="px-6 py-4 font-bold text-gray-600 text-xs uppercase tracking-wider text-center">Metode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($laporan as $trx)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-indigo-600 text-sm">{{ $trx->no_penjualan }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900">{{ $trx->tanggal->format('d M Y') }}</span>
                                <span class="text-xs text-gray-400">{{ $trx->created_at->format('H:i') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">
                                    {{ substr($trx->user->name ?? '?', 0, 1) }}
                                </div>
                                <span class="text-sm text-gray-700">{{ $trx->user->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 max-w-xs truncate">
                                @foreach($trx->details as $idx => $detail)
                                    {{ $detail->barang->nama_barang }} <span class="text-gray-400">({{ $detail->jumlah }})</span>{{ $loop->last ? '' : ', ' }}
                                @endforeach
                            </div>
                            <span class="text-xs text-gray-400">{{ $trx->details->count() }} jenis barang</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                             <span class="font-bold text-gray-900">Rp {{ number_format($trx->total, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs font-bold rounded-full uppercase
                                {{ $trx->metode_bayar == 'tunai' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                {{ $trx->metode_bayar }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                     <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Belum ada transaksi</h3>
                                <p class="text-sm text-gray-500 mt-1">Coba ubah filter tanggal atau lakukan transaksi baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($laporan->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $laporan->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
