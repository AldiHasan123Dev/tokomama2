 <table class="cell-border hover display nowrap" id="table-omzet">
    <!-- head -->
    <thead>
        <tr>
            <th rowspan="2">NO. </th>
            <th rowspan="2">TGL STUFFING</th>
            <th rowspan="2">NO. SURAT JALAN</th>
            <th rowspan="2">NO. INV</th>
            <th rowspan="2">TGL. INV</th>
            <th rowspan="2">No. Faktur Pajak</th>
            <th rowspan="2">NAMA KAPAL</th>
            <th rowspan="2">Cont</th>
            <th rowspan="2">Seal</th>
            <th rowspan="2">Job</th>
            <th rowspan="2">Nopol</th>
            <th rowspan="2">JENIS BARANG</th>
            <th rowspan="2">QUANTITY</th>
            <th rowspan="2">SATUAN</th>
            <th colspan="5" class="text-center border" style="border-left: 1px solid black; border-right: 1px solid black;">PENJUALAN (EXCL. PPN)</th>
            <!-- <th>CUSTOMER</th>
            <th>TUJUAN (Kota Cust)</th>
            <th>HARGA JUAL</th>
            <th>TOTAL TAGIHAN</th> -->
            <th colspan="5" class="text-center border" style="border-left: 1px solid black; border-right: 1px solid black;">PEMBELIAN (EXCL. PPN)</th>
            <!-- <th>HARGA BELI</th>
            <th>TOTAL</th>
            <th>TGL. PEMBAYARAN</th>
            <th>NO. VOUCHER</th> -->
            <th rowspan="2">MARGIN</th>
            <th colspan="4" class="text-center border" style="border-left: 1px solid black; border-right: 1px solid black;">INCLUDE PPN</th>
            <!-- <th>HARGA BELI (PPN)</th>
            <th>MARGIN (PPN)</th> -->
            <th colspan="3" class="text-center border" style="border-left: 1px solid black; border-right: 1px solid black;">HARSAT EXCL.PPN (SATUAN ORI)</th>
            <!-- <th>BELI</th>
            <th>JUAL</th> -->
        </tr>
        <tr>
            <!-- <th>NO. </th>
            <th>TGL STUFFING</th>
            <th>NO. SURAT JALAN</th>
            <th>NO. INV</th>
            <th>No. Faktur Pajak</th>
            <th>NAMA KAPAL</th>
            <th>Cont</th>
            <th>Seal</th>
            <th>Job</th>
            <th>Nopol</th>
            <th>JENIS BARANG</th>
            <th>QUANTITY</th>
            <th>SATUAN</th> -->
            <th style="border-left: 1px solid black;">PO CUSTOMER</th>
            <th>CUSTOMER</th>
            <th>TUJUAN (Kota Cust)</th>
            <th>HARSAT JUAL</th>
            <th style="border-right: 1px solid black;">TOTAL</th>
            <th>SUPPLIER</th>
            <th>HARSAT BELI</th>
            <th>TOTAL</th>
            <th>TGL. PEMBAYARAN</th>
            <th style="border-right: 1px solid black;">NO. VOUCHER</th>
            <!-- <th>MARGIN</th> -->
            <th style="border-left: 1px solid black;">PENJUALAN</th>
            <th>PEMBELIAN</th>
            <th style="border-right: 1px solid black;">SELISIH PPN</th>
            <th style="border-right: 1px solid black;">MARGIN</th>
            <th>SATUAN</th>
            <th>BELI</th>
            <th style="border-right: 1px solid black;">JUAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($omzet as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->Transaksi->SuratJalan->tgl_sj }}</td>
            <td>{{ $item->Transaksi->SuratJalan->nomor_surat }}</td>
            <td>{{ $item->invoice }}</td>
            <td>{{ $item->tgl_invoice }}</td>
            <td>{{ $item->NSFP->nomor }}</td>
            <td>{{ $item->Transaksi->SuratJalan->nama_kapal }}</td>
            <td>{{ $item->Transaksi->SuratJalan->no_cont }}</td>
            <td>{{ $item->Transaksi->SuratJalan->no_seal }}</td>
            <td>{{ $item->Transaksi->SuratJalan->no_job }}</td>
            <td>{{ $item->Transaksi->SuratJalan->no_pol }}</td>
            <td>{{ $item->Transaksi->Barang->nama }}</td>
            <td>{{ $item->Transaksi->jumlah_beli }}</td>
            <td>{{ $item->Transaksi->satuan_beli }}</td>
            <td>{{ $item->Transaksi->SuratJalan->no_po }}</td>
            <td>{{ $item->Transaksi->SuratJalan->Customer->nama }}</td>
            <td>{{ $item->Transaksi->SuratJalan->Customer->kota }}</td>
            <td>{{ $item->Transaksi->harga_jual }}</td>
            <td>{{ $item->Transaksi->jumlah_beli * $item->Transaksi->harga_jual }}</td>
            <td>{{ $item->Transaksi->Suppliers->nama }}</td>
            <td>{{ $item->Transaksi->harga_beli }}</td>
            <td>{{ $item->Transaksi->harga_beli * $item->Transaksi->jumlah_beli }}</td>
            <td> - </td>
            <td> Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ipsum magnam tempore quibusdam. Illum ab voluptatum laborum enim beatae, numquam officiis laboriosam recusandae quo obcaecati inventore accusantium fugiat porro, nisi at. </td>
            <td>{{ ($item->Transaksi->harga_jual * $item->Transaksi->jumlah_beli) - ($item->Transaksi->harga_beli * $item->Transaksi->jumlah_beli) }}</td>
            <td>{{ ($item->Transaksi->harga_jual + ($item->Transaksi->harga_jual * 0.11)) *  $item->Transaksi->jumlah_beli }}</td>
            <td>{{ ($item->Transaksi->harga_beli + ($item->Transaksi->harga_beli * 0.11)) *  $item->Transaksi->jumlah_beli }}</td>
            <td>{{ (($item->Transaksi->harga_jual * 0.11) * $item->Transaksi->jumlah_beli) - (($item->Transaksi->harga_beli * 0.11) * $item->Transaksi->jumlah_beli) }}</td>
            <td>{{ (($item->Transaksi->harga_jual + ($item->Transaksi->harga_jual * 0.11)) *  $item->Transaksi->jumlah_beli) - (($item->Transaksi->harga_beli + ($item->Transaksi->harga_beli * 0.11)) *  $item->Transaksi->jumlah_beli) - ((($item->Transaksi->harga_jual * 0.11) * $item->Transaksi->jumlah_beli) - (($item->Transaksi->harga_beli * 0.11) * $item->Transaksi->jumlah_beli)) }}</td>
            <td>{{ $item->Transaksi->Barang->Satuan->nama_satuan }}</td>
            <td>{{ ($item->Transaksi->satuan_beli == $item->Transaksi->Barang->Satuan->nama_satuan) ? $item->Transaksi->harga_beli : $item->Transaksi->harga_beli / $item->Transaksi->Barang->value }}</td>
            <td>{{ ($item->Transaksi->satuan_beli == $item->Transaksi->Barang->Satuan->nama_satuan) ? $item->Transaksi->harga_jual : $item->Transaksi->harga_jual / $item->Transaksi->Barang->value }}</td>
        </tr>
        @endforeach
    </tbody>
</table>