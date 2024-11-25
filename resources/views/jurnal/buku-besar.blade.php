<x-Layout.layout>
    <x-keuangan.card-keuangan>
    </head>
        <x-slot:tittle>Jurnal Buku Besar</x-slot:tittle>
        <div class="overflow-x-auto">
            <form action="{{ route('buku-besar.export') }}" method="get">
                @if (isset($_GET['month']) && isset($_GET['year']) && isset($_GET['coa']))
                    <input type="hidden" name="month"
                        value="@if (isset($_GET['month'])) {{ $_GET['month'] }} @else {{ date('m') }} @endif">
                    <input type="hidden" name="year" id="y4"
                        value="@if (isset($_GET['year'])) {{ $_GET['year'] }} @else {{ date('Y') }} @endif">
                    <input type="hidden" name="coa" id="c4"
                        value="@if (isset($_GET['coa'])) {{ $_GET['coa'] }} @else {{ '' }} @endif">
                @endif

                <button class="btn bg-green-500 text-white mt-3 mb-5" type="submit"><i
                        class="fa-solid fa-file-excel"></i> Export Excel</button>
            </form>

            <div class="grid grid-cols-4">
                <div class="font-bold">Akun : </div>
                <div>
                    <select class="js-example-basic-single w-1/2" name="akun" id="coas">
                        <option value="" selected>Pilih COA</option>
                        @foreach ($coa as $c)
                            <option value="{{ $c->id }}">{{ $c->no_akun }} - {{ $c->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="font-bold">Tahun : </div>
                <div>
                    <select class="js-example-basic-single w-1/2" name="akun" id="thn">
                        <option selected value="{{ date('Y') }}">{{ date('Y') }}</option>
                        @for ($year = date('Y'); $year >= 2024; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <style>
                .table-custom {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                .table-custom thead th {
                    background-color: #f1f1f1;
                    border: 1px solid #ddd;
                    padding: 10px;
                    text-align: left;
                }

                .table-custom tbody td,
                .table-custom tbody th {
                    border: 1px solid #ddd;
                    padding: 8px;
                }

                .table-custom tbody tr:nth-child(even) {
                    background-color: #f9f9f9;
                }

                .table-custom tbody tr:hover {
                    background-color: #f1f1f1;
                }

                .table-custom th,
                .table-custom td {
                    text-align: right;
                }

                .table-responsive {
                    overflow-x: auto;
                    /* Mengizinkan scrolling horizontal */
                    -webkit-overflow-scrolling: touch;
                    /* Untuk dukungan sentuh pada perangkat mobile */
                }

                #table-buku-besar {
                    width: 100%;
                    /* Pastikan tabel mengambil lebar penuh dari kontainer */
                    border-collapse: collapse;
                    /* Menghilangkan jarak antara border sel */
                }

                #table-buku-besar th,
                #table-buku-besar td {
                    padding: 8px;
                    /* Menambah padding untuk sel */
                    border: 1px solid #ddd;
                    /* Menambah border pada sel */
                }

                #table-buku-besar th {
                    background-color: #f2f2f2;
                    /* Warna latar belakang header */
                }
            </style>

            <!-- Tabel CSS Murni -->
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jan</th>
                        <th>Feb</th>
                        <th>Mar</th>
                        <th>Apr</th>
                        <th>Mei</th>
                        <th>Jun</th>
                        <th>Jul</th>
                        <th>Agu</th>
                        <th>Sep</th>
                        <th>Okt</th>
                        <th>Nov</th>
                        <th>Des</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Saldo Awal</th>
                        @foreach ($saldo['saldo_awal'] as $item)
                            <td>{{ number_format($item, 2, '.', ',') }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Debit</th>
                        @foreach ($saldo['debit'] as $item)
                            <td>{{ number_format($item, 2, '.', ',') }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Credit</th>
                        @foreach ($saldo['kredit'] as $item)
                            <td>{{ number_format($item, 2, '.', ',') }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Saldo Akhir</th>
                        @foreach ($saldo['saldo_akhir'] as $item)
                            <td>{{ number_format($item, 2, '.', ',') }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        @php
            $coa = isset($_GET['coa']) && $_GET['coa'] != '' ? $_GET['coa'] : null;
        @endphp
        <div class="mb-6 mt-4 flex">
            <label for="month" style="font-size: 1rem; margin: 2px; margin-top: 10px">Bulan : </label>
            <form action="" method="GET">
                <input type="hidden" name="month" value="1">
                <input type="hidden" name="year" id="y1" value="{{ date('Y') }}">
                <input type="hidden" name="coa" id="c1" value="{{ $coa }}">
                <button
                    class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1 @if (isset($_GET['month']) && $_GET['month'] == 1) bg-green-500 text-white @endif">Jan</button>
            </form>
            <form action="" method="GET">
                <input type="hidden" name="month" value="2">
                <input type="hidden" name="year" id="y2" value="{{ date('Y') }}">
                <input type="hidden" name="coa" id="c2" value="{{ $coa }}">
                <button
                    class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1 @if (isset($_GET['month']) && $_GET['month'] == 2) bg-green-500 text-white @endif">Feb</button>
            </form>
            <form action="" method="GET">
                <input type="hidden" name="month" value="3">
                <input type="hidden" name="year" id="y3" value="{{ date('Y') }}">
                <input type="hidden" name="coa" id="c3" value="{{ $coa }}">
                <button
                    class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1 @if (isset($_GET['month']) && $_GET['month'] == 3) bg-green-500 text-white @endif">Mar</button>
            </form>
            <form action="" method="GET">
                <input type="hidden" name="month" value="4">
                <input type="hidden" name="year" id="y4" value="{{ date('Y') }}">
                <input type="hidden" name="coa" id="c4" value="{{ $coa }}">
                <button
                    class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1 @if (isset($_GET['month']) && $_GET['month'] == 4) bg-green-500 text-white @endif">Apr</button>
            </form>
            <form action="" method="GET">
                <input type="hidden" name="month" value="5">
                <input type="hidden" name="year" id="y5" value="{{ date('Y') }}">
                <input type="hidden" name="coa" id="c5" value="{{ $coa }}">
                <button
                    class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1 @if (isset($_GET['month']) && $_GET['month'] == 5) bg-green-500 text-white @endif">Mei</button>
            </form>
            <form action="" method="GET">
                <input type="hidden" name="month" value="6">
                <input type="hidden" name="year" id="y6" value="{{ date('Y') }}">
                <input type="hidden" name="coa" id="c6" value="{{ $coa }}">
                <button
                    class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1 @if (isset($_GET['month']) && $_GET['month'] == 6) bg-green-500 text-white @endif">Jun</button>
            </form>
            <form action="" method="GET">
                <input type="hidden" name="month" value="7">
                <input type="hidden" name="year" id="y7" value="{{ date('Y') }}">
                <input type="hidden" name="coa" id="c7" value="{{ $coa }}">
                <button
                    class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1 @if (isset($_GET['month']) && $_GET['month'] == 7) bg-green-500 text-white @endif">Jul</button>
            </form>
            <form action="" method="GET">
                <input type="hidden" name="month" value="8">
                <input type="hidden" name="year" id="y8" value="{{ date('Y') }}">
                <input type="hidden" name="coa" id="c8" value="{{ $coa }}">
                <button
                    class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1 @if (isset($_GET['month']) && $_GET['month'] == 8) bg-green-500 text-white @endif">Agu</button>
            </form>
            <form action="" method="GET">
                <input type="hidden" name="month" value="9">
                <input type="hidden" name="year" id="y9" value="{{ date('Y') }}">
                <input type="hidden" name="coa" id="c9" value="{{ $coa }}">
                <button
                    class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1 @if (isset($_GET['month']) && $_GET['month'] == 9) bg-green-500 text-white @endif">Sep</button>
            </form>
            <form action="" method="GET">
                <input type="hidden" name="month" value="10">
                <input type="hidden" name="year" id="y10" value="{{ date('Y') }}">
                <input type="hidden" name="coa" id="c10" value="{{ $coa }}">
                <button
                    class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1 @if (isset($_GET['month']) && $_GET['month'] == 10) bg-green-500 text-white @endif">Okt</button>
            </form>
            <form action="" method="GET">
                <input type="hidden" name="month" value="11">
                <input type="hidden" name="year" id="y11" value="{{ date('Y') }}">
                <input type="hidden" name="coa" id="c11" value="{{ $coa }}">
                <button
                    class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1"
                    @if (isset($_GET['month']) && $_GET['month'] == 11) bg-green-500 text-white @endif>Nov</button>
            </form>
            <form action="" method="GET">
                <input type="hidden" name="month" value="12">
                <input type="hidden" name="year" id="y12" value="{{ date('Y') }}">
                <input type="hidden" name="coa" id="c12" value="{{ $coa }}">
                <button
                    class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1 @if (isset($_GET['month']) && $_GET['month'] == 12) bg-green-500 text-white @endif">Des</button>
            </form>
        </div>

        <div class="table-responsive">
            <table id="table-buku-besar" class="cell-border hover display nowrap compact">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>No. Jurnal</th>
                        <th>No. Akun</th>
                        <th>Akun</th>
                        <th>Nopol</th>
                        <th>Invoice</th>
                        <th>Keterangan</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        @php
                            if ($tipe == 'D') {
                                if ($item->debit > 0) {
                                    $saldo_awal += $item->debit;
                                } else {
                                    $saldo_awal -= $item->kredit;
                                }
                            } else {
                                if ($item->debit > 0) {
                                    $saldo_awal -= $item->debit;
                                } else {
                                    $saldo_awal += $item->kredit;
                                }
                            }
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->tgl }}</td>
                            <td>{{ $item->nomor }}</td>
                            <td>{{ $item->coa->no_akun }}</td>
                            <td>{{ $item->coa->nama_akun }}</td>
                            <td>{{ $item->nopol }}</td>
                            <td>{{ $item->invoice }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td style="text-align: right;">{{ number_format($item->debit, 2, ',', '.') }}</td>
                            <td style="text-align: right;">{{ number_format($item->kredit, 2, ',', '.') }}</td>
                            <td style="text-align: right;">{{ number_format($saldo_awal, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        </div>
    </x-keuangan.card-keuangan>
    <script src="https://cdn.datatables.net/2.1.0/js/dataTables.tailwindcss.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();

            //const date = new Date();
            //const month = date.getMonth() //+ 1;
            //const year = date.getFullYear();

            $(`#coas`).on(`change`, function() {
                $(`#c1`).val($(this).val())
                $(`#c2`).val($(this).val())
                $(`#c3`).val($(this).val())
                $(`#c4`).val($(this).val())
                $(`#c5`).val($(this).val())
                $(`#c6`).val($(this).val())
                $(`#c7`).val($(this).val())
                $(`#c8`).val($(this).val())
                $(`#c9`).val($(this).val())
                $(`#c10`).val($(this).val())
                $(`#c11`).val($(this).val())
                $(`#c12`).val($(this).val())
            });

            $(`#thn`).on(`change`, function() {
                $(`#y1`).val($(this).val())
                $(`#y2`).val($(this).val())
                $(`#y3`).val($(this).val())
                $(`#y4`).val($(this).val())
                $(`#y5`).val($(this).val())
                $(`#y6`).val($(this).val())
                $(`#y7`).val($(this).val())
                $(`#y8`).val($(this).val())
                $(`#y9`).val($(this).val())
                $(`#y10`).val($(this).val())
                $(`#y11`).val($(this).val())
                $(`#y12`).val($(this).val())
            })

            const searchParams = new URLSearchParams(window.location.search);
            let month = searchParams.get("month");
            let year = searchParams.get("year");
            let coa = searchParams.get("coa");
console.log(month, year, coa);

            var table = $('#table-buku-besar').DataTable({
                pageLength: 10,
            });

            function submitForm() {
                var form = document.getElementById('myForm');
                var coaInput = document.getElementById('coa');
                coaInput.value = encodeURIComponent(coaInput.value);
                form.submit();
            }
        });
    </script>
     </script>
</x-Layout.layout>
