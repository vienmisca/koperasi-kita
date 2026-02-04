@extends('layouts.app')

@section('content')
<div class="animate-fade-in-down">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <span class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">📊</span> History Pembelian
            </h1>
            <p class="text-gray-500 mt-2 ml-14">Rekap detail transaksi penjualan harian.</p>
        </div>
        
        <div class="flex items-center gap-3 bg-white p-2 rounded-xl border border-gray-100 shadow-sm">
            <form action="{{ route('kasir.laporan') }}" method="GET" class="flex items-center gap-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <input type="date" 
                           name="date" 
                           value="{{ $date }}" 
                           class="pl-10 pr-4 py-2 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-lg text-sm text-gray-700 font-medium transition-all" 
                           onchange="this.form.submit()">
                </div>
            </form>
            <div class="h-6 w-px bg-gray-200"></div>
            <button type="button" onclick="alert('Fitur Export akan segera hadir!')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2 shadow-md shadow-indigo-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export
            </button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Pendapatan -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start justify-between group hover:border-green-200 transition-colors">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Pendapatan</p>
                <h3 class="text-2xl font-bold text-gray-900 group-hover:text-green-600 transition-colors">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-gray-400 mt-1">
                    {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start justify-between group hover:border-indigo-200 transition-colors">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Transaksi</p>
                <h3 class="text-3xl font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                    {{ number_format($totalTransaksi) }}
                </h3>
            </div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
        </div>

        <!-- Rata-rata -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start justify-between group hover:border-blue-200 transition-colors">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Rata-rata Transaksi</p>
                <h3 class="text-2xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                    Rp {{ number_format($rataRata, 0, ',', '.') }}
                </h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
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
                    @forelse($laporan as $trx)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-sm text-indigo-600 font-medium">
                            #{{ $trx->no_penjualan }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $trx->created_at->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800">
                            @php
                                $items = $trx->details->take(2);
                                $remaining = $trx->details->count() - 2;
                            @endphp
                            <div>
                                @foreach($items as $item)
                                    <span>{{ $item->barang->nama_barang ?? 'Unknown' }} <span class="text-gray-400">x{{ $item->jumlah }}</span></span>{{ !$loop->last ? ',' : '' }}
                                @endforeach
                                @if($remaining > 0)
                                    <span class="text-gray-400 text-xs ml-1">+{{ $remaining }} lainnya</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">
                            Rp {{ number_format($trx->total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 capitalize">
                            {{ $trx->metode_bayar }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200">
                                {{ ucfirst($trx->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                             <button class="text-gray-400 hover:text-indigo-600 transition-colors">
                                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                             </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-4xl mb-3 opacity-20">🧾</span>
                                <p>Belum ada data transaksi pada tanggal ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t">
            {{ $laporan->links() }}
        </div>
    </div>
</div>
@endsection
