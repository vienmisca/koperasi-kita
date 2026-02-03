<div id="mutasi-table-container">
    <div class="overflow-x-auto max-h-[70vh] custom-scrollbar relative">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold tracking-wider sticky top-0 z-10 shadow-sm">
                <tr>
                    <th class="px-6 py-4 border-b bg-gray-50">Tanggal & No. Ref</th>
                    <th class="px-6 py-4 border-b bg-gray-50">Barang</th>
                    <th class="px-6 py-4 border-b bg-gray-50 text-center">Jenis</th>
                    <th class="px-6 py-4 border-b bg-gray-50 text-center">Masuk / Keluar</th>
                    <th class="px-6 py-4 border-b bg-gray-50 text-center">Stok Akhir</th>
                    <th class="px-6 py-4 border-b bg-gray-50">Keterangan</th>
                    <th class="px-6 py-4 border-b bg-gray-50">User</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($mutasi as $m)
                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out group">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $m->tanggal->format('d/m/Y') }}</div>
                        <div class="text-xs text-indigo-600 font-mono mt-0.5 font-bold">{{ $m->no_mutasi ?? '-' }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $m->ref_id ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $m->barang->nama_barang ?? 'Barang Dihapus' }}</div>
                        <div class="text-xs text-gray-500 flex gap-2 items-center mt-1">
                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[10px] font-mono border border-gray-200">{{ $m->barang->kode_barang ?? 'N/A' }}</span>
                            <span class="text-gray-300">|</span>
                            <span>{{ $m->lokasi ?? 'Gudang Utama' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($m->jenis == 'MASUK')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                {{ $m->sumber == 'adjustment' ? 'PENYESUAIAN (+)' : 'STOK MASUK' }}
                            </span>
                        @elseif($m->jenis == 'KELUAR')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                {{ $m->sumber == 'adjustment' ? 'PENYESUAIAN (-)' : 'STOK KELUAR' }}
                            </span>
                        @elseif($m->jenis == 'TRANSFER')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                TRANSFER
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                {{ $m->jenis }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($m->jenis == 'MASUK' || ($m->jenis == 'TRANSFER' && $m->jumlah < 0)) 
                            {{-- Logic adjustment: Transfer usually means out from source, so negative? But here we display absolute --}}
                            {{-- Controller stored absolute quantity. Need verify logic. Assume Masuk = + , Keluar = - --}}
                             <span class="text-green-600 font-bold text-base">+{{ number_format($m->jumlah) }}</span>
                        @elseif(($m->jenis == 'ADJUSTMENT' && $m->stok_sesudah > $m->stok_sebelum))
                             <span class="text-green-600 font-bold text-base">+{{ number_format($m->jumlah) }}</span>
                        @else
                             <span class="text-red-600 font-bold text-base">-{{ number_format($m->jumlah) }}</span>
                        @endif
                        <span class="text-xs text-gray-500 ml-1 font-medium">{{ $m->satuan ?: 'pcs' }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="text-gray-900 font-bold">{{ number_format($m->stok_sesudah) }}</div>
                        <div class="text-xs text-gray-400">Prev: {{ number_format($m->stok_sebelum) }}</div>
                    </td>
                    <td class="px-6 py-4 max-w-xs truncate text-sm text-gray-600" title="{{ $m->keterangan }}">
                        {{ Str::limit($m->keterangan, 40) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                {{ substr($m->user->name ?? 'S', 0, 1) }}
                            </div>
                            <span class="truncate max-w-[100px]">{{ $m->user->name ?? 'System' }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-50 p-4 rounded-full mb-3">
                                <i class="fas fa-box-open text-3xl text-gray-300"></i>
                            </div>
                            <p class="font-medium text-gray-600">Belum ada data mutasi stok.</p>
                            <p class="text-sm text-gray-400 mt-1">Data mutasi akan muncul setelah ada transaksi.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="text-sm text-gray-500">
            Menampilkan <span class="font-bold text-gray-700">{{ $mutasi->firstItem() ?? 0 }}</span> - <span class="font-bold text-gray-700">{{ $mutasi->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-700">{{ $mutasi->total() }}</span> data
        </div>
        <div class="pagination-container">
            {{ $mutasi->appends(request()->query())->links('pagination::tailwind') }}
        </div>
        <span id="result-total" class="hidden">{{ $mutasi->total() }}</span>
    </div>
</div>
