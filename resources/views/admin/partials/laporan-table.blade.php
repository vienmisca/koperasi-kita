<div class="overflow-x-auto">
    <table class="w-full text-left whitespace-nowrap">
        <thead class="bg-gray-50/50">
            <tr>
                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">No. Transaksi</th>
                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kasir</th>
                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
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
                    {{ $sale->tanggal->format('d M Y') }}
                    <span class="text-xs text-gray-400 block">{{ $sale->created_at->format('H:i') }}</span>
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
                        $profit = $sale->details->sum(function($detail) {
                            $cost = $detail->harga_beli_snapshot ?? $detail->barang?->harga_beli ?? 0;
                            return ($detail->harga - $cost) * $detail->jumlah;
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
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">Tidak ada transaksi ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<!-- AJAX Pagination Links -->
<div class="p-4 border-t border-gray-100" id="pagination-links">
    {{ $salesHistory->links() }}
</div>
