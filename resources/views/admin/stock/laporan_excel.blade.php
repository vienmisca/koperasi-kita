<table>
    <thead>
        <tr>
            <th colspan="9" style="font-weight: bold; font-size: 16px; text-align: center; height: 30px; vertical-align: middle;">
                LAPORAN STOK BARANG KOPERASI
            </th>
        </tr>
        <tr>
            <th colspan="9" style="text-align: center;">
                Per Tanggal: {{ date('d F Y') }}
            </th>
        </tr>
        <tr>
            <th colspan="9"></th>
        </tr>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <th style="border: 1px solid #000000; text-align: center; width: 40px;">No</th>
            <th style="border: 1px solid #000000; text-align: center; width: 100px;">Kode Barang</th>
            <th style="border: 1px solid #000000; text-align: left; width: 250px;">Nama Barang</th>
            <th style="border: 1px solid #000000; text-align: center; width: 120px;">Kategori</th>
            <th style="border: 1px solid #000000; text-align: center; width: 80px;">Stok</th>
            <th style="border: 1px solid #000000; text-align: center; width: 60px;">Satuan</th>
            <th style="border: 1px solid #000000; text-align: right; width: 100px;">Harga Beli</th>
            <th style="border: 1px solid #000000; text-align: right; width: 100px;">Harga Jual</th>
            <th style="border: 1px solid #000000; text-align: right; width: 120px;">Nilai Aset</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $no = 1; 
            $totalAset = 0;
            $totalItem = 0;
        @endphp
        @foreach($barang as $item)
            @php 
                $nilaiAset = $item->stok * $item->harga_beli;
                $totalAset += $nilaiAset;
                $totalItem += $item->stok;
            @endphp
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $no++ }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $item->kode_barang }}</td>
                <td style="border: 1px solid #000000;">{{ $item->nama_barang }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $item->stok }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $item->satuan }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ $item->harga_beli }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ $item->harga_jual }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ $nilaiAset }}</td>
            </tr>
        @endforeach
        
        <tr style="background-color: #fafafa; font-weight: bold;">
            <td colspan="4" style="border: 1px solid #000000; text-align: right;">TOTAL KESELURUHAN</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $totalItem }}</td>
            <td colspan="3" style="border: 1px solid #000000; background-color: #e0e0e0;"></td>
            <td style="border: 1px solid #000000; text-align: right; font-size: 14px;">{{ $totalAset }}</td>
        </tr>
    </tbody>
</table>
