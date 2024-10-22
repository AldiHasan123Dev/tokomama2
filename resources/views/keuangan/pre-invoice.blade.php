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
        <x-slot:tittle>Pengambilan Nomor Faktur Untuk Invoice</x-slot:tittle>
        <x-slot:button>
            <form action="{{ route('invoice-transaksi.index') }}" method="get" id="form">
                <input type="hidden" name="id_transaksi" id="id_transaksi">
                <div class="flex gap-2">
                    <div class="flex-gap-2">
                        {{-- <label for="count">Jumlah Invoice</label> --}}
                        <input type="hidden" name="invoice_count" id="count" value="1"
                            class="rounded-md form-control text-center" min="1" style="height: 28px">
                    </div>
                    <button type="submit" class="btn font-semibold bg-green-500 btn-sm text-white mt-4">Buat Draf
                        Invoice</button>
                </div>
            </form>
        </x-slot:button>

        <div class="overflow-x-auto">
            <div class="table-responsive">
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
                            align: 'center'
                        },
                        {
                            name: 'DT_RowIndex',
                            index: 'DT_RowIndex',
                            label: 'No.',
                            width: 40,
                            align: 'center'
                        },
                        {
                            name: 'nomor_surat',
                            index: 'nomor_surat',
                            label: 'Nomor Surat'
                        },
                        {
                            name: 'customer',
                            index: 'customer',
                            label: 'Customer'
                        },
                        {
                            name: 'nama_barang',
                            index: 'nama_barang',
                            label: 'Nama Barang'
                        },
                        {
                            name: 'sisa',
                            index: 'sisa',
                            label: 'Sisa'
                        },
                        {
                            name: 'harga_jual',
                            index: 'harga_jual',
                            label: 'Harga Jual'
                        },
                        {
                            name: 'subtotal',
                            index: 'subtotal',
                            label: 'Subtotal'
                        },
                        {
                            name: 'nama_kapal',
                            index: 'nama_kapal',
                            label: 'Nama Kapal'
                        },
                        {
                            name: 'no_cont',
                            index: 'no_cont',
                            label: 'No. Kontainer'
                        },
                        {
                            name: 'no_seal',
                            index: 'no_seal',
                            label: 'No. Seal'
                        },
                        {
                            name: 'no_pol',
                            index: 'no_pol',
                            label: 'No. Pol'
                        },
                        {
                            name: 'id',
                            index: 'id',
                            hidden: true
                        },
                    ],
                    pager: "#sjPager",                    // Elemen pager
        rowNum: 20,                          // Jumlah baris per halaman
        rowList: [10, 20, 50],               // Opsi jumlah baris yang bisa dipilih
        viewrecords: true,                   // Menampilkan informasi record
        autowidth: true,                     // Menyesuaikan lebar otomatis
        height: 'auto',                      // Tinggi tabel otomatis
        loadonce: false,                     // Load data secara dinamis
        serverPaging: true, 
                    pager: "#jqGridPager",
                    loadComplete: function(data) {
                        console.log('Data received from server:', data);
                        console.log('Data structure:', data.data); // Memeriksa struktur data
                    },
                    jsonReader: {
                        root: "data", // Pastikan ini sesuai dengan struktur JSON Anda
                        page: "current_page", // Sesuaikan dengan halaman saat ini
                        total: "total_pages", // Sesuaikan dengan total halaman
                        records: "total_records", // Sesuaikan dengan total records
                        id: "id" // Menggunakan field 'id' sebagai unique ID
                    }
                });

                // Menghandle form submit untuk mengambil ID transaksi yang dipilih
            });
            $('#form').submit(function (e) {
                e.preventDefault();
                var ids = $("#table-getfaktur input:checkbox:checked").map(function(){
                    return $(this).val();
                }).get();
                $('#id_transaksi').val(ids);
                this.submit();
            });
            // Menampilkan versi jQuery dan mengecek apakah jqGrid telah dimuat
            console.log('jQuery version:', $.fn.jquery);
            console.log('jqGrid loaded:', typeof $.fn.jqGrid !== 'undefined');
        </script>
    </x-slot:script>
</x-Layout.layout>
