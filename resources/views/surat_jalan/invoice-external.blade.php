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
                label: 'Invoice Supplier',
                name: 'invoice_external',
                width: 60
            },
            {
                search: true,
                label: 'No BM',
                name: 'no_bm',
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
                label: 'Jumlah Beli',
                name: 'jumlah_beli',
                width: 40,
                align: 'right'
            }
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
        id_supplier,
        nama_supplier,
        invoice_external,
        harga_beli,
        jumlah_beli,
        status_ppn,
        value_ppn,
        tgl_jurnal,
        no_bm
    ) {
        // Hitung total
        const total = parseFloat(harga_beli) * parseFloat(jumlah_beli);
        const totalFormatted = total.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });

        // Inisialisasi PPN
        let ppn = 0;
        if (tgl_jurnal && typeof tgl_jurnal === "string") {
            const date = new Date(tgl_jurnal); // Konversi string ke Date object
            if (!isNaN(date)) {
                tgl_jurnal = date.toISOString().split('T')[0]; // Format ke yyyy-MM-dd
            }
        }

        console.log({ tgl_jurnal });

        // Cek jika status_barang adalah 'ya'
        if (status_ppn === 'ya') {
            ppn = total * (parseFloat(value_ppn) / 100); // Hitung PPN
        }

        const ppnFormatted = ppn.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });

        // Menyusun HTML dialog
        $('#dialog').html(`
            <div id="my_modal_5" title="Edit Data">
                <form id="editForm" action="{{ route('surat-jalan-external.data.edit') }}" method="post">
                    @csrf
                    <input type="hidden" name="id_supplier" value="${id_supplier}" />
                     <label class="input border flex items-center gap-2 mt-3">
                        No BM :
                        <input type="text" name="no_bm" value="${no_bm}" class="border-none" readonly />
                    </label>
                    <label class="input border flex items-center gap-2 mt-3">
                        Nama Supplier :
                        <input type="text" name="nama_supplier" value="${nama_supplier}" class="border-none" readonly />
                    </label>
                        <input type="hidden" id="ppn" name="ppn" value="${ppnFormatted}" class="border-none" readonly />
                    <label class="input border flex items-center gap-2 mt-3">
                        Invoice External :
                        <input type="text" id="invoice_external" name="invoice_external" value="${invoice_external}" required class="border-none" autofocus />
                    </label>
                    <label class="input border flex items-center gap-2 mt-3">
                        Tanggal:
                        <input type="date" id="tgl_invx" name="tgl_invx" required value="${tgl_jurnal}" class="w-full border-none"/>
                    </label>
                    <button type="submit" class="btn bg-green-400 text-white font-semibold w-full mt-2">Edit</button>
                </form>
            </div>
        `);
        $('#editForm').on('submit', function (e) {
    e.preventDefault(); // Prevent the form from submitting immediately
    let invoiceExternal = $('#invoice_external').val().trim();

    // Check if invoice_external is empty or not
    if (invoiceExternal === "") {
        alert("Invoice external harus diisi terlebih dahulu!"); // Show alert if empty
    } else {
        // Show confirmation dialog
        let confirmMessage = `Apakah Anda yakin dengan invoice external ini?`;
        if (confirm(confirmMessage)) {
            // Submit the form if confirmed
            this.submit();  // Manually submit the form
        }
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
