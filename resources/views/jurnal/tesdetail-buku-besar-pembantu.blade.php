<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Detail Jurnal Buku Besar Pembantu: {{ $customer->nama }}</x-slot:tittle>
        <div class="overflow-x-auto">
            <table id="table-detail-buku-besar" class="w-full">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $detail)
                        <tr>
                            <td>{{ $detail->tgl }}</td>
                            <td>{{ $detail->invoice }}</td>
                            <td>{{ $detail->debit }}</td>
                            <td>{{ $detail->kredit }}</td>
                            <td>{{ $detail->keterangan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-keuangan.card-keuangan>
</x-Layout.layout>
