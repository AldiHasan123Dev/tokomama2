<x-layout.layout>
    <div id="exp-jurnal"></div>

    <head>
        <!-- Include jQuery and jqGrid libraries -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/css/ui.jqgrid.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/jquery.jqgrid.min.js"></script>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>

    <style>
        .modal {
            top: 0;
            left: 0;
            width: 40%;
            z-index: 1000;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 800px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-family: Arial, sans-serif;
        }

        .kembali-button {
            display: inline-block;
            padding: 12px 10px;
            background-color: #ad0f0f;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .kembali-button:hover {
            background-color: #761408;
        }

        .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            background: none;
            border: none;
            color: #333;
            cursor: pointer;
        }

        /* Form labels and containers */
        .form-label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .select-field {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
            font-size: 14px;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
            font-size: 14px;
        }

        /* Submit button */
        .submit-button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        /* Label for extra info */
        .label-info {
            font-size: 12px;
            color: red;
        }
    </style>

    {{-- <x-keuangan.card-keuangan>
        <x-slot:tittle>List Stock</x-slot:tittle>
        <div class="overflow-x-auto">
            <a class="mt-3 mb-2 mr-3 btn bg-green-500 text-white font-bold hover:bg-gray-700 mb-2px" href="{{ route('barang_masuk') }}">Input Barang Masuk</a>
            <!-- Adjusted table ID and pager ID for jqGrid -->
            <table id="jqGrid" class="table"></table>
            <div id="jqGridPager"></div>
        </div>
    </x-keuangan.card-keuangan> --}}

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Konfirmasi Penerimaan</x-slot:tittle>
        <div class="overflow-x-auto">
            <dialog id="my_modal_5" class="modal">
                <div class="modal-box w-11/12 max-w-2xl pl-10">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>
                    <h3 class="text-lg font-bold">Input Harga Beli: <span id="barang"></span></h3>
                    <label class="form-control w-full max-w">
                        <div class="form-label">
                            <span class="form-label">Harga Beli</span>
                        </div>
                        <input type="text" class="input-field" id="harga_beli" oninput="formatRibuan(this)"
                            onclick="this.select()" />
                    </label>
                    <button type="button" class="submit-button" onclick="updateTransaksi()">Update</button>
                </div>
            </dialog>

            <dialog id="my_modal_1" class="modal">
                <div class="modal-box w-11/12 max-w-2xl pl-10">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>
                    <h3 class="text-lg font-bold">Edit Jumlah Beli: <span id="barang1"></span></h3>
                    <label class="form-control w-full max-w">
                        <div class="form-label">
                            <span class="form-label">Jumlah Beli</span>
                        </div>
                        <input type="text" class="input-field" id="jumlah_beli"
                            onclick="this.select()" required/>
                    </label>
                    <label class="form-control w-full max-w">
                        <div class="form-label">
                            <span class="form-label">Status</span>
                        </div>
                        <input type="text" class="input-field" id="status" required/>
                    </label>
                    <button type="button" class="submit-button" onclick="updateTransaksi1()">Update</button>
                </div>
            </dialog>
            <!-- Adjusted table ID and pager ID for jqGrid -->
            <table id="jqGrid1" class="table"></table>
            <div id="jqGridPager1"></div>
        </div>
    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script>
            $(function () {
    const table1 = $("#jqGrid1").jqGrid({
        url: "{{ route('stock.data1') }}", // URL untuk data JSON dari controller
        datatype: "json",
        mtype: "GET",
        colModel: [
            {
                label: 'No',
                name: 'index',
                align: 'center',
                width: 20
            },
                {
                label: 'Status',
                name: 'lock',
                width: 200,
                sortable: false,
                formatter: 'unformat'
            },
            {
                search: true,
                label: 'Invoice External',
                name: 'invoice_external',
                width: 100
            },
            {
                search: true,
                label: 'No BM',
                name: 'no_bm',
                width: 100
            },
            {
                search: true,
                label: 'Barang',
                name: 'barang.nama',
                width: 100
            },
            {
                search: true,
                label: 'Volume Masuk',
                name: 'total_beli',
                width: 80,
                align: "right"
            },
            {
                search: true,
                label: 'Volume Keluar',
                name: 'total_jual',
                width: 80,
                align: "right"
            },
            {
                search: true,
                label: 'Satuan Beli',
                name: 'satuan_beli',
                width: 70,
                align: "center"
            },
            {
                search: true,
                label: 'Satuan Jual',
                name: 'satuan_jual',
                width: 70,
                align: "center"
            },
            {
                search: true,
                label: 'Sisa',
                name: 'sisa',
                width: 50,
                align: "right"
            },
            {
                search: true,
                label: 'Harga Beli',
                name: 'total_harga_beli',
                width: 80,
                align: "right",
                formatter: "number",
                formatoptions: {
                    decimalSeparator: ".",
                    thousandsSeparator: ",",
                    decimalPlaces: 2, // Jumlah angka di belakang koma
                    defaultValue: "0.00" // Nilai default jika kosong
                }
            },
            {
                search: true,
                label: 'Harga Jual',
                name: 'total_harga_jual',
                width: 80,
                align: "right",
                formatter: "number",
                formatoptions: {
                    decimalSeparator: ".",
                    thousandsSeparator: ",",
                    decimalPlaces: 2, // Jumlah angka di belakang koma
                    defaultValue: "0.00" // Nilai default jika kosong
                }
            }
        ],
        pager: "#jqGridPager1", // Link pager dengan ID yang benar
        rowNum: 20,
        rowList: [50, 100, 500], // Pilihan jumlah baris
        height: 'auto', // Tinggi tabel otomatis menyesuaikan
        viewrecords: true, // Menampilkan jumlah total data
        autowidth: true, // Menyesuaikan lebar tabel dengan kontainer
        caption: "Stocks Table", // Judul tabel
        loadonce: true, // Memuat semua data hanya sekali
        search: true, // Mengaktifkan toolbar pencarian
        toolbar: [true, "top"], // Menampilkan toolbar di bagian atas tabel
        filterToolbar: true, // Mengaktifkan filter toolbar
        gridComplete: function () {
            // Optional: Tambahkan logika jika diperlukan
        }
    });

    // Aktifkan filter toolbar (opsional jika sudah diaktifkan di opsi)
    $("#jqGrid1").jqGrid('filterToolbar');
});

function inputTarif(id_transaksi, beli, nama_barang,satuan_beli) {
                id = id_transaksi;
                $('#harga_beli').val(beli);
                nama_barang = nama_barang.replace(/\+/g, ' ').replace(/%40/g, '@')
                    .trim(); // Menghapus '+' dan mengganti '%40' dengan '@'

                // Mengatur innerHTML
                document.getElementById('barang').innerHTML = `${nama_barang} (Harga PER - ${satuan_beli})`;
                my_modal_5.showModal();
            }
            function inputTarif1(id_transaksi, jumlah, nama_barang,satuan_beli) {
                id = id_transaksi;
                $('#jumlah_beli').val(jumlah);
                nama_barang = nama_barang.replace(/\+/g, ' ').replace(/%40/g, '@')
                    .trim(); // Menghapus '+' dan mengganti '%40' dengan '@'

                // Mengatur innerHTML
                document.getElementById('barang1').innerHTML = `${nama_barang} (Harga PER - ${satuan_beli})`;
                my_modal_1.showModal();
            }
            function updateTransaksi() {
                if (confirm('Apakah anda yakin?')) {
                    $.ajax({
                        type: "PUT",
                        url: "{{ route('transaksi.update') }}",
                        data: {
                            id: id,
                            _token: "{{ csrf_token() }}",
                            harga_beli: getCleanNumber($('#harga_beli').val()),
                            // harga_jual: getCleanNumber($('#harga_jual').val()),
                            // margin: getCleanNumber($('#profit').val()),
                        },
                        success: function(response) {
                            refreshTable();
                            alert("Update Berhasil!");
                            my_modal_5.close();
                        }
                    });
                }
            }
            function updateTransaksi1() {
    let jumlahBeli = getCleanNumber($('#jumlah_beli').val());
    let status = $('#status').val();

    // Validasi apakah jumlah beli dan status sudah diisi
    if (!jumlahBeli || status === "") {
        alert("Silakan isi jumlah beli dan status terlebih dahulu!");
        return; // Hentikan eksekusi jika input kosong
    }

    if (confirm('Apakah Anda yakin?')) {
        $.ajax({
            type: "PUT",
            url: "{{ route('transaksi.update') }}",
            data: {
                id: id,
                _token: "{{ csrf_token() }}",
                jumlah_beli: jumlahBeli,
                sisa: jumlahBeli, // Sisa mengikuti jumlah beli
                stts: status,
            },
            success: function(response) {
                refreshTable();
                alert("Update Berhasil!");
                my_modal_1.close();
            }
        });
    }
}

            function formatRibuan(input) {
                let angka = input.value.replace(/,/g, ''); // Hapus koma
                input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Format dengan koma
            }

            function getCleanNumber(value) {
                // Ganti koma dengan kosong dan titik terakhir dengan koma
                const cleanValue = value.replace(/,/g, '').replace(/\.(?=.*\.)/g, ''); // Hapus koma
                const decimalIndex = value.lastIndexOf('.');
                if (decimalIndex !== -1) {
                    return parseFloat(cleanValue); // Menggunakan parseFloat untuk angka desimal
                }
                return parseInt(cleanValue) || 0; // Ubah ke integer, default 0 jika NaN
            }
            function refreshTable() {
    // Pastikan tabel sudah diinisialisasi sebelum dipanggil
    if ($("#jqGrid1").getGridParam) {
        $("#jqGrid1")
            .setGridParam({
                datatype: 'json', // Atur ulang tipe data agar data baru diambil
            })
            .trigger("reloadGrid"); // Muat ulang tabel
    } else {
        console.error("Tabel jqGrid1 belum diinisialisasi.");
    }
}

        </script>
    </x-slot:script>

</x-layout.layout>
