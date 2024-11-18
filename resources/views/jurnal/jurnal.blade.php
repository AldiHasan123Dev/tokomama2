<x-Layout.layout>
    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    </head>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Menu Jurnal</x-slot:tittle>
        <div class="overflow-x-auto">
            <a href="{{ route('jurnal-manual.index') }}">
                <button class="btn bg-green-500 text-white font-bold hover:bg-green-700 mt-5">Input Jurnal</button>
            </a>
            <a href="/invoice-external">
                <button class="btn bg-yellow-500 text-white font-bold hover:bg-gray-700">Jurnal Uang Muka</button>
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

            <table id="coa_table" class="cell-border hover display nowrap compact"></table>
            <div id="coaPager"></div>
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
    <script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
    <script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.3/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/2.1.0/js/dataTables.tailwindcss.js"></script>
    <script>
        $(document).ready(function() {
            var table = $("#coa_table").jqGrid({
    url: "{{ route('jurnal.data') }}", // Specify the URL to fetch data
    datatype: "json",
    mtype: "GET",
    colNames: [
        'No',
        'Tanggal', 
        'Tipe', 
        'Nomor', 
        'No. Akun', 
        'Nama Akun', 
        'Invoice', 
        'Debit', 
        'Kredit', 
        'Keterangan', 
        'Invoice Supplier', 
        'Nopol', 
        'Kaitan BB Pembantu', 
        'no'
    ],
    colModel: [
        { search: true, name: 'DT_RowIndex', index: 'DT_RowIndex', width: 30, align: 'center' },
        { search: true, name: 'tgl', index: 'tgl', width: 90, align: 'center' },
        { search: true, name: 'tipe', index: 'tipe', width: 50, align: 'center' },
        { search: true, name: 'nomor', index: 'nomor', width: 100, align: 'center' },
        { search: true, name: 'no_akun', index: 'no_akun', width: 70 },
        { search: true, name: 'nama_akun', index: 'nama_akun', width: 160 },
        { search: true, name: 'invoice', index: 'invoice', width: 120 },
        { search: true, name: 'debit', index: 'debit', width: 120, formatter: 'currency', formatoptions: { prefix: '', thousandsSeparator: '.', decimalPlaces: 2 }, align: 'right' },
        { search: true, name: 'kredit', index: 'kredit', width: 120, formatter: 'currency', align: 'right', formatoptions: { prefix: '', thousandsSeparator: '.', decimalPlaces: 2 } },
        { search: true, name: 'keterangan', index: 'keterangan', width: 200 },
        { search: true, name: 'invoice_external', index: 'invoice_external', align: 'center', width: 100 },
        { search: true, name: 'nopol', index: 'nopol', width: 100, align: 'center' },
        { search: true, name: 'keterangan_buku_besar_pembantu', index: 'keterangan_buku_besar_pembantu', align: 'center', width: 100 },
        { search: true, name: 'no', index: 'no', hidden: true } // Hidden column
    ],
    pager: "#coaPager",
    rowNum: 20, // Jumlah baris per halaman
    rowList: [10, 20, 50], // Opsi jumlah baris yang bisa dipilih
    viewrecords: true, // Menampilkan informasi record
    autowidth: true, // Menyesuaikan lebar otomatis
    height: 'auto', // Tinggi tabel otomatis
    loadonce: true,
    serverPaging: true,
    jsonReader: {
        repeatitems: false,
        root: "data",
        page: "current_page",
        total: "last_page",
        records: "total"
    },
    loadComplete: function(data) {
        console.log("Load complete: ", data); // Menampilkan data yang diterima saat grid selesai dimuat
        $("#coa_table").jqGrid('filterToolbar');
    },
    ajaxGridOptions: {
        beforeSend: function() {
            console.log("Fetching data from server..."); // Log saat data sedang diambil
        },
        error: function(xhr, status, error) {
            console.log("Error fetching data: ", error); // Log jika ada error saat pengambilan data
        }
    },
    onSelectRow: function(id) {
        const rowData = $("#coa_table").jqGrid('getRowData', id);
        console.log("Selected row data: ", rowData); // Menampilkan data baris yang dipilih
        $('#nomor').val($.trim(rowData.nomor));
        $('.btn').removeClass('hidden');
        $('#print').attr('href', "{{ route('invoice.print', ['id' => ':id']) }}".replace(':id', rowData.id));
    }
}).navGrid('#coaPager', { // Opsi untuk navigasi pada pager
    edit: false,
    add: false,
    del: false,
    search: false, // Disable search dari navGrid
    refresh: true,
}, {}, {}, {}, {
    closeAfterSearch: true,
    closeAfterReset: true,
    searchOnEnter: true,
});

// Fungsi untuk menyegarkan tabel
function refreshTable() {
    $("#coa_table").trigger("reloadGrid", [{ page: 1 }]);
}




            var MonJNL = $('#monitoring_JNL').DataTable({})

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
