<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Invoice</th>
            <th>NPWP</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Nama NPWP</th>
            <th>Alamat NPWP</th>
            <th>Tanggal Faktur</th>
            <th>Tujuan</th>
            <th>Uraian</th>
            <th>Faktur</th>
            <th>Sub Total (Rp)</th>
            <th>PPN</th>
            <th>Nominal PPN (Rp)</th>
            <th>Total (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $item)
            @php
                // Deklarasi variabel untuk kondisi PPN
                $ppnStatus = $item->transaksi->barang->status_ppn; 
                $ppnValue = $item->transaksi->barang->value_ppn;
                $subtotal = $item->subtotal;
                $ppnRate = 0.11; // PPN rate default 11%
                
                // Perhitungan nilai PPN dan total
                if ($ppnStatus === 'ya' && $ppnValue == 11) {
                    $ppnAmount = $subtotal * $ppnRate;
                    $total = $subtotal + $ppnAmount;
                    $ppnDisplay = '11%';
                } elseif ($ppnStatus === 'tidak' && $ppnValue == 11) {
                    $ppnAmount = 0;
                    $total = $subtotal;
                    $ppnDisplay = '0%';
                } else {
                    $ppnAmount = 0;
                    $total = $subtotal;
                    $ppnDisplay = '0%';
                }
            @endphp
            <tr>
                <th>{{ $loop->iteration }}</th>
                <th>{{ $item->invoice }}</th>
                <th>{{ $item->transaksi->suratJalan->customer->npwp }}</th>
                <th>{{ $item->transaksi->suratJalan->customer->nik }}</th>
                <th>{{ $item->transaksi->suratJalan->customer->nama }}</th>
                <th>{{ $item->transaksi->suratJalan->customer->nama_npwp }}</th>
                <th>{{ $item->transaksi->suratJalan->customer->alamat_npwp }}</th>
                <th>{{ $item->tgl_invoice }}</th>
                <th>{{ $item->transaksi->suratJalan->customer->nama }}</th>
                <th>{{ $item->transaksi->barang->nama }}</th>
                <th>{{ $item->nsfp->nomor }}</th>
                <th>{{ $subtotal }}</th> <!-- Format angka -->
                <th>{{ $ppnDisplay }}</th>
                <th>{{ $ppnAmount }}</th> <!-- Nominal PPN -->
                <th>{{ $total }}</th> <!-- Total -->
            </tr>
        @endforeach
    </tbody>
</table>
