<table>
    <thead>
        <tr>
            <th colspan="7" style="font-weight: bold; font-size: 16px; text-align: center; height: 30px; vertical-align: middle;">
                LAPORAN PENJUALAN KOPERASI
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </th>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>
        <tr style="background-color: #4472C4; color: #ffffff; font-weight: bold;">
            <th style="border: 1px solid #000000; text-align: center; width: 130px;">Tanggal Transaksi</th>
            <th style="border: 1px solid #000000; text-align: left; width: 250px;">Nama Barang</th>
            <th style="border: 1px solid #000000; text-align: center; width: 50px;">Qty</th>
            <th style="border: 1px solid #000000; text-align: right; width: 100px;">Harga Satuan</th>
            <th style="border: 1px solid #000000; text-align: right; width: 100px;">Subtotal</th>
            <th style="border: 1px solid #000000; text-align: right; width: 120px;">Total Transaksi</th>
            <th style="border: 1px solid #000000; text-align: right; width: 100px;">Keuntungan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $trx)
            @php 
                $trxDate = $trx->created_at->format('d-m-Y H:i');
            @endphp
            @foreach($trx->details as $detail)
                @php 
                    $subtotal = $detail->harga * $detail->jumlah;
                    $buyPrice = $detail->barang->harga_beli ?? 0;
                    $profit = ($detail->harga - $buyPrice) * $detail->jumlah;
                @endphp
                <tr>
                    <td style="border: 1px solid #000000; text-align: left;">{{ $trxDate }}</td>
                    <td style="border: 1px solid #000000; text-align: left;">{{ $detail->barang->nama_barang ?? 'Item Terhapus' }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $detail->jumlah }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ number_format($subtotal, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ number_format($trx->total, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ number_format($profit, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
