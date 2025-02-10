<x-Layout.layout>
    <style>
        tr.selected {
            background-color: lightskyblue !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Tabel Invoice</x-slot:tittle>
        <div class="overflow-x-auto">
            <a href="#" target="_blank" class="btn bg-green-400 text-white my-5 py-4 font-bold hidden"
                id="print">
                <i class="fas fa-print"></i> Cetak Invoice</button>
            </a>
            {{-- <a href="#" target="_blank" class="btn bg-green-400 text-white my-5 py-4 font-bold hidden"
                id="print1">
                <i class="fas fa-print"></i> Cetak Surat Penerimaan</button>
            </a> --}}
            <table class="table" id="surat_jalan_table"></table>
            <div id="sjPager"></div>
        </div>
    </x-keuangan.card-keuangan>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.3/js/dataTables.select.js"></script>
    <script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
    <script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#surat_jalan_table").jqGrid({
                url: "{{ route('invoice.data') }}", // URL server-side
                datatype: "json", // Format data
                mtype: "POST", // Request method
                postData: {
                    invoice: "1", // Parameter invoice yang tetap
                    _search: "false" // Parameter pencarian awal
                },
                colModel: [{
                        search: true,
                        label: 'No',
                        name: 'DT_RowIndex',
                        width: 50,
                        key: true,
                        align: 'center'
                    },
                    {
                        search: true,
                        label: 'NSFP',
                        name: 'nsfp',
                        width: 150
                    },
                    {
                        search: true,
                        label: 'Nomor Jurnal',
                        name: 'nomor',
                        width: 150
                    },
                    {
                        search: true,
                        label: 'Invoice',
                        name: 'invoice',
                        width: 150
                    },
                    {
                        search: true,
                        label: 'Subtotal',
                        name: 'subtotal',
                        width: 150,
                        align: 'right'
                    },
                    {
                        search: true,
                        label: 'PPN',
                        name: 'ppn',
                        width: 100,
                        align: 'right'
                    },
                    {
                        search: true,
                        label: 'Total',
                        name: 'total',
                        width: 150,
                        align: 'right'
                    },
                ],
                pager: "#sjPager", // Elemen pager
                rowNum: 20, // Jumlah baris per halaman
                rowList: [10, 20, 50], // Opsi jumlah baris yang bisa dipilih
                viewrecords: true, // Menampilkan informasi record
                autowidth: true, // Menyesuaikan lebar otomatis
                height: 'auto', // Tinggi tabel otomatis
                loadonce: true, // Load data secara dinamis
                serverPaging: true, // Paging server-side
                jsonReader: {
                    repeatitems: false,
                    root: "data", // Lokasi array data
                    page: "current_page", // Halaman saat ini
                    total: "last_page", // Total halaman
                    records: "total", // Total data
                },
                loadComplete: function(data) {
                    // Log respons saat load complete
                    console.log("Data loaded:", data);
                },
                onSelectRow: function(id) {
                    var rowData = $("#surat_jalan_table").jqGrid('getRowData', id);
                    $('.btn').removeClass('hidden');
                    $('#print').attr('href', "{{ url('keuangan/cetak-invoice') }}" + '/?invoice=' +
                        rowData.invoice);
                    $('#print1').attr('href', "{{ url('keuangan/cetak-invoicesp') }}" + '/?invoice=' +
                        rowData.invoice);
                }
            }).navGrid('#sjPager', { // Opsi untuk navigasi pada pager
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

            // Menambahkan filterToolbar
            $("#surat_jalan_table").jqGrid('filterToolbar');
        });
    </script>
</x-Layout.layout>
