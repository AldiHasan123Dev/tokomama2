<x-Layout.layout>
    <style>
        .table-responsive td {
            white-space: nowrap;
            /* Mencegah teks terpotong dan menjaga agar tetap dalam satu baris */
        }

        .d-flex.flex-row {
            gap: 10px;
            /* Menambah jarak antar elemen dalam baris */
        }

        /* Atur lebar minimum kolom agar cukup besar untuk data */
        th,
        td {
            min-width: 150px;
        }
    </style>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Buku Besar Pembantu</x-slot:tittle>
        <div class="overflow-x-auto">
            <form method="GET" action="{{ route('buku-besar-pembantu.index') }}" id="filter-form">
                <div class="flex justify-between w-full">
                    <div>
                        <label class="form-control w-full max-w-xs">
                            <div class="label w-full">
                                <span class="label-text">Subjek</span>
                            </div>
                            <select class="js-example-basic-single w-64" name="state" id="subject-select">
                                <option value="customer"
                                    {{ request('state', 'customer') == 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="supplier" {{ request('state') == 'supplier' ? 'selected' : '' }}>Supplier
                                </option>
                                <option value="ncs" {{ request('state') == 'ncs' ? 'selected' : '' }}>
                                    Non-Customer/Supplier</option>
                            </select>
                        </label>

                    </div>
                    <div>
                        <label class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text">Akun</span>
                            </div>
                            <select class="js-example-basic-single" name="coa_id" id="coa-select">
                                @foreach ($coa as $c)
                                    <option value="{{ $c->id }}"
                                        {{ $selectedCoaId == $c->id ? 'selected' : '' }}>{{ $c->no_akun }} -
                                        {{ $c->nama_akun }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <div>
                        <label class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text">Tahun</span>
                            </div>
                            <select class="js-example-basic-single w-64" name="year" id="year-select">
                                @for ($year = 2030; $year >= 2023; $year--)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                        {{ $year }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                </div>
                <label for="tahun" class="mr-2 margin-top:40px">Bulan:</label>
                @foreach (['Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'Mei' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Okt' => 10, 'Nov' => 11, 'Des' => 12] as $monthName => $monthNumber)
                    <button
                        class="btn my-5 py-4 font-bold border-black {{ $selectedMonth == $monthNumber ? 'bg-green-600 text-white' : 'bg-white text-black hover:bg-green-600 hover:text-white' }}"
                        name="month" value="{{ $monthNumber }}" type="submit"
                        {{ $selectedMonth == $monthNumber ? 'disabled' : '' }}>
                        {{ $monthName }}
                    </button>
                @endforeach
            </form>
            <!-- Tabel Customer -->
            <div id="customer-table"
                class="{{ request('state', 'customer') == 'supplier' || request('state') == 'ncs' ? 'hidden' : '' }}">
                <a href="{{ route('export.customers', ['year' => $selectedYear, 'month' => $selectedMonth, 'coa_id' => $selectedCoaId]) }}"
                    class="btn bg-green-500 text-white mt-3 mb-5  py-4  h-12 w-32 ">
                    <i class="fa-solid fa-file-excel mr-2"></i>
                    Ekspor Excel
                </a>
                <table id="table-customer" class="w-full">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Customer</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                            <th>Saldo</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $key => $customer)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $customer->nama }}</td>
                                <td>{{ number_format($customer->debit, 0, ',', '.') }}</td>
                                <td>{{ number_format($customer->kredit, 0, ',', '.') }}</td>
                                <td>
                                    @if ($tipe == 'K')
                                        {{ number_format($customer->kredit - $customer->debit, 0, ',', '.') }}
                                    @else
                                        {{ number_format($customer->debit - $customer->kredit, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td>
                                    <button class="bg-blue-400 text-white py-1 px-3 rounded hover:bg-blue-300"
                                        onclick="showDetailModal({{ $customer->id }}, {{ $selectedYear }}, {{ $selectedMonth }}, {{ $selectedCoaId }}, 'customer')">
                                        Detail
                                    </button>
                                    <button class="bg-green-400 text-white py-1 px-3 rounded hover:bg-green-300"
                                        onclick="showSummaryModal({{ $customer->id }}, {{ $selectedYear }}, {{ $selectedMonth }}, {{ $selectedCoaId }}, 'customer')">
                                        Pasangan
                                    </button>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-center">Total</th>
                            <th>
                                {{ number_format($customers->sum('debit'), 0, ',', '.') }}
                            </th>
                            <th>
                                {{ number_format($customers->sum('kredit'), 0, ',', '.') }}
                            </th>
                            <th>
                                @if ($tipe == 'K')
                                    {{ number_format($customers->sum('kredit') - $customers->sum('debit'), 0, ',', '.') }}
                                @else
                                    {{ number_format($customers->sum('debit') - $customers->sum('kredit'), 0, ',', '.') }}
                                @endif
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- Tabel Supplier -->
            <div id="supplier-table"
                class="{{ request('state', 'customer') == 'customer' || request('state') == 'ncs' ? 'hidden' : '' }}">
                <a href="{{ route('export.supplier', ['year' => $selectedYear, 'month' => $selectedMonth, 'coa_id' => $selectedCoaId]) }}"
                    class="btn bg-green-500 text-white mt-3 mb-5  py-4  h-12 w-32 ">
                    <i class="fa-solid fa-file-excel mr-2"></i>
                    Export Excel
                </a>

                <table id="table-supplier" class="w-full">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Supplier</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                            <th>Saldo</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $key => $supplier)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $supplier->nama }}</td>
                                <td>{{ number_format($supplier->debit, 0, ',', '.') }}</td>
                                <td>{{ number_format($supplier->kredit, 0, ',', '.') }}</td>
                                <td>
                                    @if ($tipe == 'K')
                                        {{ number_format($supplier->kredit - $supplier->debit, 0, ',', '.') }}
                                    @else
                                        {{ number_format($supplier->debit - $supplier->kredit, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td>
                                    <button class="bg-blue-400 text-white py-1 px-3 rounded hover:bg-blue-300"
                                        onclick="showDetailModal({{ $supplier->id }}, {{ $selectedYear }}, {{ $selectedMonth }}, {{ $selectedCoaId }}, 'supplier')">
                                        Detail
                                    </button>
                                    <button class="bg-green-400 text-white py-1 px-3 rounded hover:bg-green-300"
                                        onclick="showSummaryModal({{ $supplier->id }}, {{ $selectedYear }}, {{ $selectedMonth }}, {{ $selectedCoaId }}, 'supplier')">
                                        Pasangan
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-center">Total</th>
                            <th>
                                {{ number_format($suppliers->sum('debit'), 2, ',', '.') }}
                            </th>
                            <th>
                                {{ number_format($suppliers->sum('kredit'), 2, ',', '.') }}
                            </th>
                            <th>
                                @if ($tipe == 'K')
                                    {{ number_format($suppliers->sum('kredit') - $suppliers->sum('debit'), 0, ',', '.') }}
                                @else
                                    {{ number_format($suppliers->sum('debit') - $suppliers->sum('kredit'), 0, ',', '.') }}
                                @endif
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>


            <!-- Tabel NCS (Non-Customer/Supplier) -->
            <div id="ncs-table" class="{{ request('state') == 'ncs' ? '' : 'hidden' }}">
                <a href="{{ route('export.ncs', ['year' => $selectedYear, 'month' => $selectedMonth, 'coa_id' => $selectedCoaId]) }}"
                    class="btn bg-green-500 text-white mt-3 mb-5 py-4 h-12 w-32">
                    <i class="fa-solid fa-file-excel mr-2"></i>
                    Export Excel
                </a>
                <div class="table-responsive" style="overflow-x: auto; border: 1px solid black;">
                    <table id="table-ncs"style="border: 1px solid black;">
                        <thead>
                            <tr>
                                <th style="border: 1px solid black; width: 30px;" class="text-center">No.</th>
                                <th style="border: 1px solid black;" class="text-center">Tanggal </th>
                                <th style="border: 1px solid black;" class="text-center">Nomor </th>
                                <th style="border: 1px solid black;" class="text-center">Keterangan</th>
                                <th style="border: 1px solid black;" class="text-center">Debit</th>
                                <th style="border: 1px solid black;" class="text-center">Kredit</th>
                                <th style="border: 1px solid black;" class="text-center">Saldo</th>
                                <th style="border: 1px solid black;" class="text-center">Tanggal </th>
                                <th style="border: 1px solid black;" class="text-center">Nomor </th>
                                <th style="border: 1px solid black;" class="text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($ncsDetails as $key => $ncs)
                                @php $nomorUtama = $key + 1; @endphp <!-- Variabel untuk nomor utama -->
                                <tr>
                                    <td style="border: 1px solid black;" class="text-center">{{ $nomorUtama }}</td>
                                    <td style="border: 1px solid black;" class="text-center">{{ $ncs['tgl'] }}</td>
                                    <td style="border: 1px solid black;" class="text-center">{{ $ncs['nomor'] }}</td>
                                    <td style="border: 1px solid black;">{{ $ncs['keterangan'] }}</td>
                                    <td style="border: 1px solid black; 
    @if ($ncs['debit'] != $ncs['kredit']) background-color: red; color:white; @endif
"
                                        class="text-right">
                                        {{ number_format($ncs['debit'], 2, ',', '.') }}
                                    </td>
                                    <td style="border: 1px solid black; 
    @if ($ncs['debit'] != $ncs['kredit']) background-color: red; color:white; @endif
"
                                        class="text-right">
                                        {{ number_format($ncs['kredit'], 2, ',', '.') }}
                                    </td>

                                    <td style="border: 1px solid black;" class="text-right">
                                        @if ($tipe == 'K')
                                            {{ number_format($ncs['kredit'] - $ncs['debit'], 2, ',', '.') }}
                                        @else
                                            {{ number_format($ncs['debit'] - $ncs['kredit'], 2, ',', '.') }}
                                        @endif
                                    </td>

                                    <td style="border: 1px solid black;" class="text-center">
                                        @if (isset($ncs['details']) && count($ncs['details']) > 0)
                                            @foreach ($ncs['details'] as $detail)
                                                <span>{{ $detail['tgl'] }}</span><br>
                                            @endforeach
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td style="border: 1px solid black;" class="text-center">
                                        @if (isset($ncs['details']) && count($ncs['details']) > 0)
                                            @foreach ($ncs['details'] as $detail)
                                                <span>{{ $detail['nomor'] }}</span><br>
                                            @endforeach
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td style="border: 1px solid black;">
                                        @if (isset($ncs['details']) && count($ncs['details']) > 0)
                                            @foreach ($ncs['details'] as $detail)
                                                <span>{{ $detail['keterangan'] }}</span><br>
                                            @endforeach
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="4" style="border: 1px solid black;" class="text-center">Total</th>
                                <th style="border: 1px solid black;" class="text-right">
                                    {{ number_format($ncsDebitTotal, 2, ',', '.') }}</th>
                                <th style="border: 1px solid black;" class="text-right">
                                    {{ number_format($ncsKreditTotal, 2, ',', '.') }}</th>
                                <th style="border: 1px solid black;" class="text-right">
                                    @php
                                        $totalSaldo =
                                            $tipe == 'K'
                                                ? $ncsKreditTotal - $ncsDebitTotal
                                                : $ncsDebitTotal - $ncsKreditTotal;
                                    @endphp
                                    {{ number_format($totalSaldo, 2, ',', '.') }}
                                </th>
                                <th style="border: 1px solid black;" colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>





        </div>
    </x-keuangan.card-keuangan>

    <!-- Modal -->
    <div id="detailModal" class="hidden fixed z-10 inset-0 overflow-y-auto h-full  bg-gray-50">
        <div class="flex items-center justify-center min-h-screen border-black">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="modal-header flex justify-between items-center">
                    <h2 class="text-s font-bold" id="modalTitle">Detail Buku Besar Pembantu</h2>
                    <button class=" text-red-500 font-bold text-xl" onclick="closeModal()"> X </button>
                </div>
                <div class="modal-body">
                    <table id="table-detail-buku-besar" class="w-full border-collapse border border-gray-200">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">Tanggal (D)</th>
                                <th class="border border-gray-300 px-4 py-2">Nomor (D)</th>
                                <th class="border border-gray-300 px-4 py-2">Tanggal (K)</th>
                                <th class="border border-gray-300 px-4 py-2">Nomor (K)</th>
                                <th class="border border-gray-300 px-4 py-2">Invoice</th>
                                <th class="border border-gray-300 px-4 py-2">Debit</th>
                                <th class="border border-gray-300 px-4 py-2">Kredit</th>
                                <th class="border border-gray-300 px-4 py-2">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="modalBody" class="text-center border border-gray-300 px-4 py-2">
                            <!-- Data will be inserted here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="detailModal1" class="hidden fixed z-10 inset-0 overflow-y-auto h-full  bg-gray-50">
        <div class="flex items-center justify-center min-h-screen border-black">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="modal-header flex justify-between items-center">
                    <h2 class="text-s font-bold" id="modalTitle">Detail Buku Besar Pembantu</h2>
                    <button class=" text-red-500 font-bold text-xl" onclick="closeModal()"> X </button>
                </div>
                <div class="modal-body">
                    <table id="table-detail-buku-besar" class="w-full border-collapse border border-gray-200">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2">Nomor</th>
                                <th class="border border-gray-300 px-4 py-2">Invoice</th>
                                <th class="border border-gray-300 px-4 py-2">Debit</th>
                                <th class="border border-gray-300 px-4 py-2">Kredit</th>
                                <th class="border border-gray-300 px-4 py-2">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="modalBody1" class="text-center border border-gray-300 px-4 py-2">
                            <!-- Data will be inserted here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();

            // Periksa nilai awal dan tampilkan tabel yang sesuai
            const initialSubject = $('#subject-select').val();
            if (initialSubject === 'customer') {
                $('#customer-table').removeClass('hidden');
                $('#supplier-table').addClass('hidden');
                $('#ncs-table').addClass('hidden');
            } else if (initialSubject === 'supplier') {
                $('#customer-table').addClass('hidden');
                $('#supplier-table').removeClass('hidden');
                $('#ncs-table').addClass('hidden');
            } else if (initialSubject === 'ncs') {
                $('#customer-table').addClass('hidden');
                $('#supplier-table').addClass('hidden');
                $('#ncs-table').removeClass('hidden');
            }

            // Tampilkan tabel yang sesuai berdasarkan pilihan subjek
            $('#subject-select').on('change', function() {
                const selectedSubject = $(this).val();
                if (selectedSubject === 'customer') {
                    $('#customer-table').removeClass('hidden');
                    $('#supplier-table').addClass('hidden');
                    $('#ncs-table').addClass('hidden');
                } else if (selectedSubject === 'supplier') {
                    $('#customer-table').addClass('hidden');
                    $('#supplier-table').removeClass('hidden');
                    $('#ncs-table').addClass('hidden');
                } else if (selectedSubject === 'ncs') {
                    $('#customer-table').addClass('hidden');
                    $('#supplier-table').addClass('hidden');
                    $('#ncs-table').removeClass('hidden');
                }
                $('#filter-form').submit(); // Mengirimkan form untuk memuat data
            });

            $('#coa-select, #year-select').on('change', function() {
                $('#filter-form').submit();
            });

            // Initialize DataTable for all tables
            $('#table-customer').DataTable();
            $('#table-supplier').DataTable();
            $('#table-ncs').DataTable(); // Inisialisasi DataTable untuk tabel NCS
        });


        function number_format(number, decimals = 0, dec_point = ',', thousands_sep = '.') {
            number = parseFloat(number).toFixed(decimals);

            let nstr = number.split('.');
            let x1 = nstr[0];
            let x2 = nstr.length > 1 ? dec_point + nstr[1] : '';
            let rgx = /(\d+)(\d{3})/;

            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');
            }

            return x1 + x2;
        }

        function showDetailModal(entityId, year, month, coaId, state, keterangan, debit, kredit) {
            $.ajax({
                url: '{{ route('buku-besar-pembantu.showDetail', ':id') }}'.replace(':id', entityId),
                method: 'GET',
                data: {
                    year: year,
                    month: month,
                    coa_id: coaId,
                    state: state
                },
                success: function(response) {
                    $('#modalTitle1').text('Detail Buku Besar Pembantu: ' + response.entityName + ' || Akun: ' +
                        response.coa.no_akun + ' - ' + response.coa.nama_akun);
                    $('#modalBody1').empty();

                    const totalDebit = response.totalDebit;
                    const totalKredit = response.totalKredit;
                    const saldo = response.view_total;
                    console.log(response.details)

                    let rows = '';

                    // Periksa state untuk menampilkan detail yang sesuai
                    if (state === 'ncs') {
                        response.details.forEach(detail => {
                            rows += `<tr>
                    <td class="border border-gray-300 px-4 py-2">${detail.tgl}</td>
                    <td class="border border-gray-300 px-4 py-2">${detail.nomor}</td>
                    <td class="border border-gray-300 px-4 py-2">${number_format(detail.debit)}</td>
                    <td class="border border-gray-300 px-4 py-2">${number_format(detail.kredit)}</td>
                    <td class="border border-gray-300 px-4 py-2 text-start">${detail.keterangan || '-'}</td>
                    <td class="border border-gray-300 px-4 py-2 text-start">${detail.keterangan_buku_besar_pembantu || '-'}</td>
                </tr>`;
                        });
                    } else if (state === 'customer') {
                        response.details.forEach(detail => {
                            rows += `<tr>
                    <td class="border border-gray-300 px-4 py-2">${detail.tgl}</td>
                    <td class="border border-gray-300 px-4 py-2">${detail.nomor}</td>
                    <td class="border border-gray-300 px-4 py-2">${detail.invoice || '-'}</td>
                    <td class="border border-gray-300 px-4 py-2">${number_format(detail.debit)}</td>
                    <td class="border border-gray-300 px-4 py-2">${number_format(detail.kredit)}</td>
                    <td class="border border-gray-300 px-4 py-2 text-start">${detail.keterangan || '-'}</td>
                </tr>`;
                        });
                    } else {
                        response.details.forEach(detail => {
                            rows += `<tr>
                    <td class="border border-gray-300 px-4 py-2">${detail.tgl}</td>
                    <td class="border border-gray-300 px-4 py-2">${detail.nomor}</td>
                    <td class="border border-gray-300 px-4 py-2">${detail.invoice_external || '-'}</td>
                    <td class="border border-gray-300 px-4 py-2">${number_format(detail.debit)}</td>
                    <td class="border border-gray-300 px-4 py-2">${number_format(detail.kredit)}</td>
                    <td class="border border-gray-300 px-4 py-2 text-start">${detail.keterangan || '-'}</td>
                </tr>`;
                        });
                    }


                    rows += `<tr>
                        <td colspan="2" class="border border-gray-300 px-4 py-2 font-bold">Total</td>
                        <td class="border border-gray-300 text-right font-bold">${number_format(totalDebit)}</td>
                        <td class="border border-gray-300 px-4 text-right font-bold">${number_format(totalKredit)}</td>
                        <td class="border border-gray-300 text-right font-bold">SALDO: ${number_format(saldo)}</td>
                    </tr>`;

                    $('#modalBody1').append(rows);
                    $('#detailModal1').removeClass('hidden');
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + error);
                }
            });
        }



        function showSummaryModal(entityId, year, month, coaId, state) {
            $.ajax({
                url: '{{ route('buku-besar-pembantu.showDetail', ':id') }}'.replace(':id', entityId),

                method: 'GET',
                data: {
                    year: year,
                    month: month,
                    coa_id: coaId,
                    state: state,
                    type: 'summary' // Tambahkan parameter type
                },
                success: function(response) {
                    if (response.coa) {
                        $('#modalTitle').text('Pasangan Buku Besar Pembantu: ' + response.entityName +
                            ' || Akun: ' + response.coa.no_akun + ' - ' + response.coa.nama_akun);
                        $('#modalBody').empty();

                        const totalDebit = response.totalDebit;
                        const totalKredit = response.totalKredit;
                        const saldo = response.view_total;

                        let mergedData = {};
                        response.details.forEach(detail => {
                            let invoice = state === 'customer' ? detail.invoice : detail
                                .invoice_external;

                            if (!mergedData[invoice]) {
                                mergedData[invoice] = {
                                    nomor: [],
                                    tgl: [],
                                    debit: 0,
                                    kredit: 0,
                                    keterangan: []
                                };
                            }
                            mergedData[invoice].nomor.push(detail.nomor);
                            mergedData[invoice].tgl.push(detail.tgl);
                            mergedData[invoice].debit += parseFloat(detail.debit);
                            mergedData[invoice].kredit += parseFloat(detail.kredit);
                            mergedData[invoice].keterangan.push(detail.keterangan || '-');
                        });

                        let rows = '';
                        for (let invoice in mergedData) {
                            rows += `<tr>
                                <td class="border border-gray-300 px-4 py-2">${mergedData[invoice].tgl[0]}</td>
                                <td class="border border-gray-300 px-4 py-2">${mergedData[invoice].nomor[0]}</td>
                                <td class="border border-gray-300 px-4 py-2">${mergedData[invoice].tgl[1] ? mergedData[invoice].tgl[1] : '-'}</td>
                                <td class="border border-gray-300 px-4 py-2">${mergedData[invoice].nomor[1] ? mergedData[invoice].nomor[1] : '-'}</td>
                                <td class="border border-gray-300 px-4 py-2">${invoice}</td>
                                <td class="border border-gray-300 text-right ${mergedData[invoice].debit !== mergedData[invoice].kredit ? 'bg-red-500 text-white' : ''}">
    ${number_format(mergedData[invoice].debit)}
</td>
<td class="border border-gray-300 text-right ${mergedData[invoice].debit !== mergedData[invoice].kredit ? 'bg-red-500 text-white' : ''}">
    ${number_format(mergedData[invoice].kredit)}
</td>

                                <td class="border border-gray-300 px-4 py-2 text-start">${mergedData[invoice].keterangan.join(', <br> ')}</td>
                            </tr>`;
                        }

                        rows += `<tr>
                            <td colspan="5" class="border border-gray-300 px-4 py-2 font-bold">Total</td>
                            <td class="border border-gray-300 text-right font-bold">${number_format(totalDebit)}</td>
                            <td class="border border-gray-300 text-right font-bold">${number_format(totalKredit)}</td>
                            <td class="border border-gray-300 px-4 py-2 text-start font-bold">SALDO: ${number_format(saldo)}</td>
                        </tr>`;

                        $('#modalBody').append(rows);
                        $('#detailModal').removeClass('hidden');
                    } else {
                        console.error('Data COA tidak ditemukan dalam respons.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + error);
                }
            });
        }




        function closeModal() {
            $('#detailModal').addClass('hidden');
            $('#detailModal1').addClass('hidden');
        }
    </script>
</x-Layout.layout>
