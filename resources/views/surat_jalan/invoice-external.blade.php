<x-Layout.layout>

    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/css/ui.jqgrid.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/jquery.jqgrid.min.js"></script>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>

    <div id="dialog"></div>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>List Invoice Supplier</x-slot:tittle>
        <div class="overflow-x-auto">
            <table id="table-supplier" class="table"></table>
            <div id="sjPager"></div>
        </div>
    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script>
            $(document).ready(function () {
    $("#table-supplier").jqGrid({
        url: "{{ route('surat-jalan-supplier.data') }}",
        datatype: "json",
        mtype: "POST",
        postData: {
            _token: "{{ csrf_token() }}",
            _search: "false",
        },
        colModel: [
            {
                search: true,
                label: 'Aksi',
                name: 'aksi',
                width: 20,
                formatter: function (cellValue) {
                    return cellValue;
                },
                sortable: false,
                align: 'center'
            },
            {
                search: true,
                label: 'Surat Jalan',
                name: 'nomor_surat',
                width: 60
            },
            {
                search: true,
                label: 'Supplier',
                name: 'supplier',
                width: 80
            },
            {
                search: true,
                label: 'Invoice Supplier',
                name: 'invoice_external',
                width: 60
            },
            {
                search: true,
                label: 'Harga Beli',
                name: 'harga_beli',
                width: 70,
                align: 'right',
                
            },
            {
                search: true,
                label: 'Jumlah Beli',
                name: 'jumlah_beli',
                width: 40,
                align: 'center'
            },
            {
                search: true,
                label: 'Sub Total',
                name: 'total',
                width: 70,
                align: 'right'
            },
            {
                search: true,
                label: 'PPN',
                name: 'ppn',
                width: 70,
                align: 'right'
            },
        ],
        pager: "#sjPager", // Elemen pager
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
        loadComplete: function (data) {
            console.log("Data loaded:", data);
            $("#table-supplier").jqGrid('filterToolbar');
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

    function refreshTable() {
        $("#table-supplier").trigger("reloadGrid", [{ page: 1 }]);
    }

    // Fungsi untuk menampilkan dialog edit
    window.getData = function (
        id_surat_jalan,
        nomor_surat,
        id_supplier,
        nama_supplier,
        invoice_external,
        harga_beli,
        jumlah_beli,
        status_ppn,
        value_ppn
    ) {
        // Hitung total
        const total = parseFloat(harga_beli) * parseFloat(jumlah_beli);
        const totalFormatted = total.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        // Inisialisasi PPN
        let ppn = 0;

        // Cek jika status_barang adalah 'ya'
        if (status_ppn === 'ya') {
            ppn = total * (parseFloat(value_ppn) / 100); // Hitung PPN
        }

        const ppnFormatted = ppn.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        // Menyusun HTML dialog
        $('#dialog').html(`
            <div id="my_modal_5" title="Edit Data">
                <form id="editForm" action="{{ route('surat-jalan-external.data.edit') }}" method="post">
                    @csrf
                    <input type="hidden" name="id_surat_jalan" value="${id_surat_jalan}" />
                    <label class="input border flex items-center gap-2 mt-3">
                        Nomor Surat :
                        <input type="text" name="nomor_surat" value="${nomor_surat}" class="border-none" readonly />
                    </label>
                    <input type="hidden" name="id_supplier" value="${id_supplier}" />
                    <label class="input border flex items-center gap-2 mt-3">
                        Nama Supplier :
                        <input type="text" name="nama_supplier" value="${nama_supplier}" class="border-none" readonly />
                    </label>
                    <label class="input border flex items-center gap-2 mt-3">
                        Sub Total :
                        <input type="text" id="total" name="total" value="${totalFormatted}" class="border-none" readonly />
                    </label>
                    <label class="input border flex items-center gap-2 mt-3">
                        PPN :
                        <input type="text" id="ppn" name="ppn" value="${ppnFormatted}" class="border-none" readonly />
                    </label>
                    <label class="input border flex items-center gap-2 mt-3">
                        Invoice External :
                        <input type="text" id="invoice_external" name="invoice_external" value="${invoice_external}" required class="border-none" autofocus />
                    </label>
                    <button type="submit" class="btn bg-green-400 text-white font-semibold w-full mt-2">Edit</button>
                </form>
            </div>
        `);
        $('#editForm').on('submit', function (e) {
            let invoiceExternal = $('#invoice_external').val().trim();
            if (invoiceExternal === "") {
                e.preventDefault(); // Mencegah pengiriman form
                alert("Invoice external harus diisi terlebih dahulu!"); // Menampilkan alert
            }
        });

        $('#my_modal_5').dialog({
            modal: true,
            width: 600,
            buttons: {
                "Cancel": function () {
                    $(this).dialog("close");
                }
            }
        });
    }
});

        </script>
    </x-slot:script>
</x-Layout.layout>
