<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Menu Jurnal</x-slot:tittle>
        <div class="overflow-x-auto">
            <a href="{{ route('jurnal-manual.index') }}">
                <button class="btn bg-green-500 text-white font-bold hover:bg-green-700 mt-5">Input Jurnal</button>
            </a>

            <a href="{{ route('jurnal.jurnal-merger') }}">
                <button class="btn bg-gray-500 text-white font-bold hover:bg-gray-700">Merge Jurnal</button>
            </a>

            <div class="flex flex-row mb-16 mt-8">
                <label for="month" class="font-bold mt-4">Bulan:</label>
                <form action="" method="GET">
                    <input type="hidden" name="month" id="m1" value="1">
                    <input type="hidden" name="year" id="y1" value="{{ date('Y') }}">
                    <button id="btn1"
                        class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1 @if (isset($_GET['month']) && $_GET['month'] == 1) bg-green-500 text-white @endif">Jan</button>
                </form>
                <form action="" method="GET">
                    <input type="hidden" name="month" id="m2" value="2">
                    <input type="hidden" name="year" id="y2" value="{{ date('Y') }}">
                    <button id="btn2"
                        class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">Feb</button>
                </form>
                <form action="" method="GET">
                    <input type="hidden" name="month" id="m3" value="3">
                    <input type="hidden" name="year" id="y3" value="{{ date('Y') }}">
                    <button id="btn3"
                        class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">Mar</button>
                </form>
                <form action="" method="GET">
                    <input type="hidden" name="month" id="m4" value="4">
                    <input type="hidden" name="year" id="y4" value="{{ date('Y') }}">
                    <button id="btn4"
                        class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">Apr</button>
                </form>
                <form action="" method="GET">
                    <input type="hidden" name="month" id="m5" value="5">
                    <input type="hidden" name="year" id="y5" value="{{ date('Y') }}">
                    <button id="btn5"
                        class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">Mei</button>
                </form>
                <form action="" method="GET">
                    <input type="hidden" name="month" id="m6" value="6">
                    <input type="hidden" name="year" id="y6" value="{{ date('Y') }}">
                    <button id="btn6"
                        class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">Jun</button>
                </form>
                <form action="" method="GET">
                    <input type="hidden" name="month" id="m7" value="7">
                    <input type="hidden" name="year" id="y7" value="{{ date('Y') }}">
                    <button id="btn7"
                        class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">Jul</button>
                </form>
                <form action="" method="GET">
                    <input type="hidden" name="month" id="m8" value="8">
                    <input type="hidden" name="year" id="y8" value="{{ date('Y') }}">
                    <button id="btn8"
                        class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">Agu</button>
                </form>
                <form action="" method="GET">
                    <input type="hidden" name="month" id="m9" value="9">
                    <input type="hidden" name="year" id="y9" value="{{ date('Y') }}">
                    <button id="btn9"
                        class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">Sep</button>
                </form>
                <form action="" method="GET">
                    <input type="hidden" name="month" id="m10" value="10">
                    <input type="hidden" name="year" id="y10" value="{{ date('Y') }}">
                    <button id="btn10"
                        class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">Okt</button>
                </form>
                <form action="" method="GET">
                    <input type="hidden" name="month" id="m11" value="11">
                    <input type="hidden" name="year" id="y11" value="{{ date('Y') }}">
                    <button id="btn11"
                        class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">Nov</button>
                </form>
                <form action="" method="GET">
                    <input type="hidden" name="month" id="m12" value="12">
                    <input type="hidden" name="year" id="y12" value="{{ date('Y') }}">
                    <button id="btn12"
                        class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">Des</button>
                </form>

                <div class="w-full ml-10 mt-3">
                    <b>Tahun : </b>
                    <select class="js-example-basic-single w-1/2" name="akun" id="thn">
                        <option selected value="{{ date('Y') }}">{{ date('Y') }}</option>
                        @for ($year = date('Y'); $year >= 2024; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mb-10 flex justify-between">
                {{-- <div>
                    Filter Tanggal : <input type="date" name="tanggal" id="tanggal">
                </div> --}}
                <div>
                    <div class="flex flex-row">
                        <label for="month" class="font-bold mt-3">Tipe : </label>
                        <form action="" method="GET">
                            <input type="hidden" name="tipe" value="JNL">
                            <input type="hidden" name="month" id="tm1"
                                value="{{ isset($_GET['month']) ? $_GET['month'] : '' }}">
                            <input type="hidden" name="year" id="y2-1" value="{{ date('Y') }}">
                            <button
                                class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">JNL</button>
                        </form>
                        <form action="" method="GET">
                            <input type="hidden" name="tipe" value="BKK">
                            <input type="hidden" name="month" id="tm2"
                                value="{{ isset($_GET['month']) ? $_GET['month'] : '' }}">
                            <input type="hidden" name="year" id="y2-2" value="{{ date('Y') }}">
                            <button
                                class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">BKK</button>
                        </form>
                        <form action="" method="GET">
                            <input type="hidden" name="tipe" value="BKM">
                            <input type="hidden" name="month" id="tm3"
                                value="{{ isset($_GET['month']) ? $_GET['month'] : '' }}">
                            <input type="hidden" name="year" id="y2-3" value="{{ date('Y') }}">
                            <button
                                class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">BKM</button>
                        </form>
                        <form action="" method="GET">
                            <input type="hidden" name="tipe" value="BBK">
                            <input type="hidden" name="month" id="tm4"
                                value="{{ isset($_GET['month']) ? $_GET['month'] : '' }}">
                            <input type="hidden" name="year" id="y2-4" value="{{ date('Y') }}">
                            <button
                                class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">BBK</button>
                        </form>
                        <form action="" method="GET">
                            <input type="hidden" name="tipe" value="BBM">
                            <input type="hidden" name="month" id="tm5"
                                value="{{ isset($_GET['month']) ? $_GET['month'] : '' }}">
                            <input type="hidden" name="year" id="y2-5" value="{{ date('Y') }}">
                            <button
                                class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl mx-1">BBM</button>
                        </form>
                    </div>
                </div>
                <div>
                    <form action="{{ route('jurnal.edit') }}" method="get" class="ml-10">
                        <input type="hidden" name="nomor" id="nomor">
                        <button class="btn bg-yellow-500 text-white font-bold hover:bg-yellow-700" id="edit">Edit
                            Jurnal</button>
                    </form>
                </div>
            </div>

            <table id="coa_table" class="cell-border hover display nowrap compact">
                <!-- head -->
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Nomor</th>
                        <th>No. Akun</th>
                        <th>Nama Akun</th>
                        <th>Invoice</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                        <th>Keterangan</th>
                        <th>Invoice Supplier</th>
                        <th>Nopol</th>
                        <th>Kaitan BB Pembantu</th>
                        <th class="hidden">no</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $d)
                        <tr>
                            <td>{{ $d->tgl }}</td>
                            <td>{{ $d->tipe }}</td>
                            <td>{{ $d->nomor }}</td>
                            <td>{{ $d->no_akun }}</td>
                            <td>{{ $d->nama_akun }}</td>
                            <td> {{ $d->invoice == 0 ? '' : ($d->invoice ?? '-') }}</td>
                            <td class="text-end">{{ number_format($d->debit, 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($d->kredit, 2, ',', '.') }}</td>
                            <td>{{ $d->keterangan }}</td>
                            <td>
                                {{ $d->invoice_external == 0 ? '' : ($d->invoice_external ?? '-') }}
                            </td>                            
                            <td>{{ $d->nopol }}</td>
                            <td>{{ $d->keterangan_buku_besar_pembantu }}</td>
                            <td class="hidden">{{ $d->no }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-keuangan.card-keuangan>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Monitoring jurnal</x-slot:tittle>
        <table id="monitoring_JNL" class="cell-border hover display nowrap compact">
            <thead>
                <th>Total Debet</th>
                <th>Total Kredit</th>
                <th>Nomor JNL Terakhir</th>
            </thead>
            <tbody>
                <tr>
                    <td>{{ number_format($MonJNL->sum('debit'), 2, ',', '.') }}</td>
                    <td>{{ number_format($MonJNL->sum('kredit'), 2, ',', '.') }}</td>
                    <td>{{ $LastJNL->max('no') }}</td>
                </tr>
            </tbody>
        </table>
        <center>
            <div class="overflow-x-auto">
                <h1 class="font-bold text-xl">Cek Jurnal Tidak Balance</h1>
                <table border="1" class="table">
                    <thead>
                        <tr>
                            <th width="30%"><div align="center">#</div></th>
                           
                            <th width="70%"><div align="left">Nomor Jurnal</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($notBalance))
                            <tr>
                                <td colspan="2"><div align="center">Semua Jurnal Balance</div></td>
                            </tr>
                        @else
                            @foreach ($notBalance as $nb)
                                <tr class="hover">
                                    <td><div align="center">{{ $loop->iteration }}</div></td>
                                    
									<td><div align="left">{{ $nb }}</div></td>
                                </tr>
                            @endforeach
                        @endif


                    </tbody>
              </table>
            </div>
        </center>
    </x-keuangan.card-keuangan>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.3/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/2.1.0/js/dataTables.tailwindcss.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#coa_table').DataTable({
                order: [
                    [0]
                ],
                pageLength: 100,
                select: true,

            });

            var MonJNL = $('#monitoring_JNL').DataTable({})

            $('#coa_table tbody').on('click', 'tr', function() {
                const row = table.row(this).data();
                console.log(row);
                // $('#tipe').val(row[1]);
                // $('#no').val(row[12]);
                $('#nomor').val($.trim(row[2]).trim(/%2F/g, ''));
                $('.btn').removeClass('hidden');
                $('#print').attr('href', "{{ route('invoice.print', ['id' => ':id']) }}".replace(':id', row
                    .id));
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
                $(`#y2-1`).val($(this).val())
                $(`#y2-2`).val($(this).val())
                $(`#y2-3`).val($(this).val())
                $(`#y2-4`).val($(this).val())
                $(`#y2-5`).val($(this).val())
            })

        });
    </script>

</x-Layout.layout>
