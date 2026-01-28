<table>
    <thead>
        <tr>
            <th colspan="8" style="font-weight: bold; font-size: 16px; text-align: center; height: 30px; vertical-align: middle;">
                LAPORAN PENJUALAN KOPERASI
            </th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000000;">
            <th style="border: 1px solid #000000; width: 150px; text-align: center;">No Transaksi</th>
            <th style="border: 1px solid #000000; width: 120px; text-align: center;">Waktu</th>
            <th style="border: 1px solid #000000; width: 150px; text-align: center;">Kasir</th>
            <th style="border: 1px solid #000000; width: 200px;">Nama Barang</th>
            <th style="border: 1px solid #000000; width: 100px; text-align: right;">Harga Satuan</th>
            <th style="border: 1px solid #000000; width: 80px; text-align: center;">Qty</th>
            <th style="border: 1px solid #000000; width: 120px; text-align: right;">Subtotal</th>
            <th style="border: 1px solid #000000; width: 100px; text-align: center;">Metode</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $trx)
            @foreach($trx->details as $index => $detail)
                <tr>
                    {{-- Merge cell logic for transaction details --}}
                    @if($index === 0)
                        <td rowspan="{{ $trx->details->count() }}" style="border: 1px solid #000000; vertical-align: top;">
                            {{ $trx->no_penjualan }}
                        </td>
                        <td rowspan="{{ $trx->details->count() }}" style="border: 1px solid #000000; vertical-align: top; text-align: center;">
                            {{ $trx->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td rowspan="{{ $trx->details->count() }}" style="border: 1px solid #000000; vertical-align: top;">
                            {{ $trx->user->name ?? 'Unknown' }}
                        </td>
                    @endif

                    <td style="border: 1px solid #000000;">{{ $detail->barang->nama_barang ?? 'Item Terhapus' }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $detail->harga }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $detail->jumlah }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $detail->harga * $detail->jumlah }}</td>

                    @if($index === 0)
                        <td rowspan="{{ $trx->details->count() }}" style="border: 1px solid #000000; vertical-align: top; text-align: center;">
                            {{ strtoupper($trx->metode_bayar) }}
                        </td>
                    @endif
                </tr>
            @endforeach
            <tr style="background-color: #fafafa;">
                <td colspan="6" style="border: 1px solid #000000; text-align: right; font-weight: bold;">TOTAL TRANSAKSI</td>
                <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $trx->total }}</td>
                <td style="border: 1px solid #000000; background-color: #fafafa;"></td>
            </tr>
        @endforeach
    </tbody>
</table>
