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
        <x-slot:tittle>SJ siapp draft invoice</x-slot:tittle>
        <x-slot:button>
            <form action="{{ route('pending.invoice.harga_jual') }}" method="get" id="form">
                <input type="hidden" name="id_transaksi" id="id_transaksi">
                <div class="flex gap-2">
                    <div class="flex-gap-2">
                        <input type="hidden" name="invoice_count" id="count" value="1"
                            class="rounded-md form-control text-center" min="1" style="height: 28px">
                    </div>
                    <button type="submit" class="btn font-semibold bg-green-500 btn-sm text-white mt-4">Buat Draf
                        Invoice</button>
                </div>
            </form>
        </x-slot:button>

        <div class="overflow-x-auto mt-5">
            <div class="table-responsive">
                <!-- Checkbox "Select All" di luar tabel -->
                <div class="mb-2">
                    <input type="checkbox" class="m-2" id="select-all" /> Pilih Semua
                </div>
                <table class="table" id="table-getfaktur"></table>
                <div id="jqGridPager"></div>
            </div>
        </div>
    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
        <script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>

        <script>
            $(document).ready(function() {
                // Inisialisasi jqGrid
                let table = $("#table-getfaktur").jqGrid({
                    url: "{{ route('invoice.pre-invoice') }}", // Ganti dengan URL API yang benar
                    mtype: "GET",
                    datatype: "json",
                    colModel: [{
                            name: 'checkbox',
                            index: 'checkbox',
                            label: 'Pilih',
                            width: 50,
                            align: 'center',
                            formatter: function() {
                                return '<input type="checkbox" class="row-checkbox" />'; // Checkbox untuk setiap baris
                            }
                        },
                        {
                            search: true,
                            name: 'DT_RowIndex',
                            index: 'DT_RowIndex',
                            label: 'No.',
                            width: 20,
                            align: 'center'
                        },
                        {
                            search: true,
                            name: 'nomor_surat',
                            index: 'nomor_surat',
                            width: 130,
                            label: 'Nomor Surat',
                            align: 'center'
                        },
                        {
                            search: true,
                            name: 'customer',
                            index: 'customer',
                            label: 'Customer',
                            width: 130
                        },
                        {
                            search: true,
                            name: 'nama_barang',
                            index: 'nama_barang',
                            width: 200,
                            label: 'Nama Barang'
                        },
                        {
                            search: true,
                            name: 'sisa',
                            index: 'sisa',
                            label: 'Jml',
                            width: 50,
                            align: 'right'
                        },
                        {
                            search: true,
                            name: 'harga_jual',
                            index: 'harga_jual',
                            label: 'Harga Jual',
                            width: 100,
                            formatoptions: {
                                decimalPlaces: 4,
                                thousandsSeparator: ',',
                            },
                            align: 'right',
                            formatter: function(cellValue) {
                                if (!isNaN(cellValue)) {
                                    let parts = cellValue.toString().split('.');
                                    let formattedInteger = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g,
                                        ",");
                                    if (parts.length > 1) {
                                        if (parseInt(parts[1]) !== 0) {
                                            return `${formattedInteger}.<span style="color: red;">${parts[1]}</span>`;
                                        }
                                    }
                                    return formattedInteger + (parts[1] ? '.' + parts[1] : '');
                                }
                                return cellValue;
                            }
                        },
                        {
                            search: true,
                            name: 'subtotal',
                            index: 'subtotal',
                            label: 'Subtotal',
                            formatoptions: {
                                decimalPlaces: 4,
                                thousandsSeparator: ',',
                            },
                            align: 'right',
                            formatter: 'number'
                        },
                        // {
                        //     search: true,
                        //     name: 'nama_kapal',
                        //     index: 'nama_kapal',
                        //     label: 'Nama Kapal',
                        //     align: 'center'
                        // },
                        // {
                        //     search: true,
                        //     name: 'no_cont',
                        //     index: 'no_cont',
                        //     label: 'No. Count',
                        //     align: 'center'
                        // },
                        // {
                        //     search: true,
                        //     name: 'no_seal',
                        //     index: 'no_seal',
                        //     label: 'No. Seal',
                        //     align: 'center'
                        // },
                        {
                            search: true,
                            name: 'no_pol',
                            index: 'no_pol',
                            label: 'No. Pol',
                            align: 'center'
                        },
                        {
                            name: 'id',
                            index: 'id',
                            hidden: true
                        },
                    ],
                    pager: "#jqGridPager",
                    rowNum: 20,
                    rowList: [10, 20, 50],
                    viewrecords: true,
                    autowidth: true,
                    height: 'auto',
                    loadonce: true,
                    serverPaging: true,
                    loadComplete: function(data) {
                        console.log('Data received from server:', data);
                        console.log('Data structure:', data.data);
                    },
                    jsonReader: {
                        root: "data",
                        page: "current_page",
                        total: "total_pages",
                        records: "total_records",
                        id: "id"
                    },
                });

                // Menambahkan filterToolbar
                $("#table-getfaktur").jqGrid('navGrid', '#jqGridPager', {
                    edit: false,
                    add: false,
                    del: false,
                    search: false,
                    refresh: true
                });
                $("#table-getfaktur").jqGrid('filterToolbar');

                // Menghandle checkbox "Select All"
                $('#select-all').change(function() {
                    var checked = $(this).is(':checked');
                    $('.row-checkbox').prop('checked', checked);
                });
            });

            // Menghandle form submit untuk mengambil ID transaksi yang dipilih
            $('#form').submit(function(e) {
                e.preventDefault(); // Mencegah form untuk submit otomatis
                var ids = $("#table-getfaktur input:checkbox:checked").map(function() {
                    return $(this).closest('tr').find('td:last-child')
                .text(); // Mengambil ID dari kolom terakhir
                }).get();

                // Cek apakah ids kosong
                if (ids.length === 0) {
                    // Jika tidak ada ID yang dipilih, tampilkan alert
                    alert('Silakan pilih item data terlebih dahulu.');
                } else {
                    // Jika ada ID, set nilai dan submit form
                    $('#id_transaksi').val(ids);
                    this.submit();
                }
            });


            // Menampilkan versi jQuery dan mengecek apakah jqGrid telah dimuat
            console.log('jQuery version:', $.fn.jquery);
            console.log('jqGrid loaded:', typeof $.fn.jqGrid !== 'undefined');
        </script>
    </x-slot:script>
</x-Layout.layout>
