<table>
    <thead>
        <tr>
            <th colspan="11" style="font-weight: bold; font-size: 16px; text-align: center; height: 30px; vertical-align: middle;">
                LAPORAN PENJUALAN KOPERASI
            </th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center;">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">No</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">Tanggal</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">Waktu</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">No Transaksi</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">Kasir</th>
            <th style="border: 1px solid #000000; text-align: left; font-weight: bold;">Nama Barang</th>
            <th style="border: 1px solid #000000; text-align: right; font-weight: bold;">Harga</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">Qty</th>
            <th style="border: 1px solid #000000; text-align: right; font-weight: bold;">Subtotal</th>
            <th style="border: 1px solid #000000; text-align: right; font-weight: bold;">Total Faktur</th>
            <th style="border: 1px solid #000000; text-align: right; font-weight: bold;">Dibayar</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $no = 1; 
            $currentDate = null;
            $dailyTotal = 0;
        @endphp

        @foreach($data as $trx)
            @php 
                $trxDate = $trx->created_at->format('d/m/Y'); 
                
                // Show daily total IF date changes
                if ($currentDate !== null && $currentDate !== $trxDate) {
                    echo '<tr>';
                    echo '<td colspan="8" style="border: 1px solid #000000; text-align: right; font-weight: bold; background-color: #e6e6e6;">TOTAL PENDAPATAN TANGGAL ' . $currentDate . '</td>';
                    echo '<td style="border: 1px solid #000000; text-align: right; font-weight: bold; background-color: #e6e6e6;">' . $dailyTotal . '</td>';
                    echo '<td colspan="2" style="border: 1px solid #000000; background-color: #e6e6e6;"></td>';
                    echo '</tr>';
                    echo '<tr><td colspan="11"></td></tr>'; // Separator
                    $dailyTotal = 0;
                }
                $currentDate = $trxDate;
            @endphp

            @foreach($trx->details as $key => $detail)
                @php 
                    $subtotal = $detail->harga * $detail->jumlah;
                    $dailyTotal += $subtotal;
                @endphp
                <tr>
                    @if($key === 0)
                        <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">{{ $no++ }}</td>
                        <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">{{ $trxDate }}</td>
                        <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">{{ $trx->created_at->format('H:i') }}</td>
                        <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">{{ $trx->no_penjualan }}</td>
                        <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">{{ $trx->user->name ?? '-' }}</td>
                    @else
                        <td style="border: 1px solid #000000; border-top: none;"></td>
                        <td style="border: 1px solid #000000; border-top: none;"></td>
                        <td style="border: 1px solid #000000; border-top: none;"></td>
                        <td style="border: 1px solid #000000; border-top: none;"></td>
                        <td style="border: 1px solid #000000; border-top: none;"></td>
                    @endif

                    <td style="border: 1px solid #000000;">{{ $detail->barang->nama_barang ?? 'Item Terhapus' }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $detail->harga }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $detail->jumlah }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $subtotal }}</td>

                    @if($key === 0)
                        <td style="border: 1px solid #000000; text-align: right; vertical-align: top;">{{ $trx->total }}</td>
                        <td style="border: 1px solid #000000; text-align: right; vertical-align: top;">{{ $trx->bayar }}</td>
                    @else
                        <td style="border: 1px solid #000000; border-top: none;"></td>
                        <td style="border: 1px solid #000000; border-top: none;"></td>
                    @endif
                </tr>
            @endforeach
        @endforeach

        {{-- Final Total --}}
        @if($currentDate !== null)
            <tr>
                <td colspan="8" style="border: 1px solid #000000; text-align: right; font-weight: bold; background-color: #e6e6e6;">TOTAL PENDAPATAN TANGGAL {{ $currentDate }}</td>
                <td style="border: 1px solid #000000; text-align: right; font-weight: bold; background-color: #e6e6e6;">{{ $dailyTotal }}</td>
                <td colspan="2" style="border: 1px solid #000000; background-color: #e6e6e6;"></td>
            </tr>
        @endif
    </tbody>
</table>
