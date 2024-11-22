<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>Tipe</th>
            <th>Nomor</th>
            <th>No akun</th>
            <th>Nama akun</th>
            <th>Invoice</th>
            <th>Debit</th>
            <th>Keterangan</th>
            <th>Invoice Supplier</th>
            <th>Nopol</th>
            <th>Kaitan BB Pembantu</th>
        </tr>
    </thead>
    <tbody>
        @foreach($jurnal as $item)
            <tr>
                <th>{{ $loop->iteration }}</th>
                <th>{{ $item->tgl}}</th>
                <th>{{ $item->tipe}}</th>
                <th>{{ $item->nomor }}</th>
                <th>{{ $item->coa->no_akun }}</th>
                <th>{{ $item->coa->nama_akun }}</th>
                <th>{{ $item->invoice }}</th>
                <th>{{ $item->debit }}</th>
                <th>{{ $item->kredit }}</th>
                <th>{{ $item->keterangan }}</th>
                <th>{{ $item->invoice_external }}</th>
                <th>{{ $item->nopol }}</th>
                <th>{{ $item->keterangan_buku_besar_pembantu }}</th>
            </tr>
        @endforeach
    </tbody>
</table>
