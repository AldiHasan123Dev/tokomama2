<x-Layout.layout>
    <head>
        <!-- Include jQuery CDN -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Include jqGrid CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/css/ui.jqgrid.min.css" />
        <!-- Include jqGrid JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/jquery.jqgrid.min.js"></script>
        <!-- Include jQuery UI CSS (optional for styling) -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    </head>

    <div id="dialog"></div>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>List Invoice External</x-slot:tittle>
        <div class="overflow-x-auto">
            <table id="table-supplier" class="table"></table>
        </div>
    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script>
$(document).ready(function () {
    $("#table-supplier").jqGrid({
        url: "{{ route('surat-jalan-supplier.data') }}", // URL server-side
        datatype: "json", // Format data
        mtype: "POST", // Request method
        postData: { 
            _token: "{{ csrf_token() }}"
        },
        colModel: [
            { 
                label: 'Aksi',
                width: 30,
                height: "auto",
                formatter: function (cellvalue, options, rowObject) {
                    return `
                        <button onclick="getData('${rowObject.id_surat_jalan}', '${rowObject.nomor_surat}', '${rowObject.id_supplier}', '${rowObject.nama_supplier}', '${rowObject.invoice_external}')" id="edit" class="text-yellow-400 font-semibold self-end"><i class="fa-solid fa-pencil"></i>EDIT</button>
                    `;
                }
            },
            { label: 'Surat Jalan', name: 'nomor_surat', width: 150 },
            { label: 'Supplier', name: 'supplier', width: 150 },
            { label: 'Invoice External', name: 'invoice_external', width: 150 }
        ],
        pager: "#sjPager", // Elemen pager
        rowNum: 10, // Jumlah baris per halaman
        rowList: [10, 50, 100, 200], // Opsi jumlah baris yang bisa dipilih
        viewrecords: true, // Menampilkan informasi record
        autowidth: true, // Menyesuaikan lebar otomatis
        height: 'auto', // Tinggi tabel otomatis
        loadonce: false, // Load data secara dinamis
        serverPaging: true, // Load data secara dinamis
        jsonReader: {
            repeatitems: false,
            root: "data", // Lokasi array data
            page: "current_page", // Halaman saat ini
            total: "last_page", // Total halaman
            records: "total" // Total data
        },
        loadComplete: function(data) {
            console.log("Data loaded:", data);
            // Memanggil filterToolbar setelah data dimuat
            $("#table-supplier").jqGrid('filterToolbar');
        }
    });

    // Opsi untuk navigasi pada pager
    $("#sjPager").navGrid('#sjPager', { 
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

    // Fungsi untuk refresh tabel
    function refreshTable() {
        $("#table-supplier").trigger("reloadGrid");
    }

    // Menambahkan tombol refresh
    $("#refreshButton").on("click", function() {
        refreshTable();
    });

    // Fungsi untuk mendapatkan data dan menampilkan modal
    window.getData = function (id_surat_jalan, nomor_surat, id_supplier, nama_supplier, invoice_external) {
        $('#dialog').html(`
            <dialog id="my_modal_5" class="modal">
                <div class="modal-box w-11/12 max-w-2xl pl-10">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>
                    <h3 class="text-lg font-bold">Edit Data</h3>
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
                            Invoice External :
                            <input type="text" id="invoice_external" name="invoice_external" value="${invoice_external}" required class="border-none" autofocus />
                        </label>
                        <button type="submit" class="btn bg-green-400 text-white font-semibold w-72 mt-2">Edit</button>
                    </form>
                </div>
            </dialog>
        `);

        // Validasi ketika form di-submit
        $('#editForm').on('submit', function(e) {
            let invoiceExternal = $('#invoice_external').val().trim();
            if (invoiceExternal === "") {
                e.preventDefault(); // Mencegah pengiriman form
                alert("Invoice external diisi terlebih dahulu"); // Menampilkan alert
            }
        });

        document.getElementById('my_modal_5').showModal();
    }
});


        </script>
    </x-slot:script>
</x-Layout.layout>
