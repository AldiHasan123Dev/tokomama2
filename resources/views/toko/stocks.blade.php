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
                    <h3 class="text-lg font-bold">Konfirmasi Penerimaan: <span id="barang1"></span></h3>
                    <label class="form-control w-full max-w">
                       
                        <!-- Menampilkan semua jumlah beli di sini -->
                        <div id="jumlah_beli_container"></div>
                    </label>
                    <label class="form-control w-full max-w">
                        <div class="form-label">
                            <span class="form-label">Status</span>
                        </div>
                        <input type="text" class="input-field" id="status" required/>
                    </label>
                    <button type="button" class="submit-button" onclick="updateTransaksi1()">Diterima</button>
                </div>
            </dialog>
            
            <x-slot:button>
                <form action="" method="get" id="form">
                    <input type="hidden" name="id_transaksi" id="id_transaksi">
                    <div class="flex gap-2">
                        <div class="flex-gap-2">
                            <input type="hidden" name="invoice_count" id="count" value="1"
                            class="rounded-md form-control text-center" min="1" style="height: 28px">
                        </div>
                        <button type="submit" class="btn font-semibold bg-green-500 btn-sm text-white mt-4"
                        >
                        Konfirmasi Penerimaan
                    </button>
                    
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
                            name: 'id',
                            index: 'id',
                            hidden: true
            },
            {
                label: 'Status',
                name: 'status',
                width: 200,
                sortable: false,
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
    $('#select-all').change(function() {
        var checked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', checked);
    });
});

// Menghandle form submit untuk mengambil ID transaksi yang dipilih
$('#form').submit(function (e) {
    e.preventDefault(); // Mencegah form untuk submit secara langsung

    var ids = $("#jqGrid1 input:checkbox:checked").map(function() {
    var rowId = $(this).closest('tr').attr('id'); // Ambil row ID
    return $("#jqGrid1").jqGrid('getCell', rowId, 'id'); // Ambil kolom 'id' yang di-hidden
}).get();

console.log("IDs:", ids);


var barang = $("#jqGrid1 input:checkbox:checked").map(function() {
    return $(this).closest('tr').find('td:eq(6)').text(); // Mengambil nama barang dari kolom "Barang"
}).get();
console.log("Barang:", barang);

var jumlahBeli = $("#jqGrid1 input:checkbox:checked").map(function() {
    return $(this).closest('tr').find('td:eq(7)').text(); // Mengambil jumlah beli dari kolom "Volume Masuk"
}).get();
console.log("Jumlah Beli:", jumlahBeli);

var satuanBeli = $("#jqGrid1 input:checkbox:checked").map(function() {
    return $(this).closest('tr').find('td:eq(9)').text(); // Mengambil satuan beli dari kolom "Satuan Beli"
}).get();
console.log("Satuan Beli:", satuanBeli);

var noBm = $("#jqGrid1 input:checkbox:checked").map(function() {
    return $(this).closest('tr').find('td:eq(5)').text(); // Mengambil nama barang dari kolom "No BM"
}).get();
console.log("No BM:", noBm);
var status = $("#jqGrid1 input:checkbox:checked").map(function() {
    return $(this).closest('tr').find('td:eq(3)').text(); // Mengambil nama barang dari kolom "No BM"
}).get();
console.log("Status :", status);

if (status.some(s => s !== "-")) {
    alert("Status sudah divalidasi!");
    return;
} 
if (ids.length > 0) {
    // Jika ada checkbox yang dicentang, tampilkan modal dengan data yang sesuai
    // Iterasi untuk semua item yang dipilih
    inputTarif(ids, jumlahBeli, barang, satuanBeli, noBm); // Tampilkan data untuk semua item yang dipilih
} 
else {
    alert("Pilih barang masuk terlebih dahulu!");
}
});

function inputTarif(ids_transaksi, jumlah, nama_barang, satuan_beli, no_bm) {
    let itemsHTML = ''; // Tempat untuk menampung data yang ingin ditampilkan

    // Menggunakan Set untuk mengambil nilai unik dari no_bm
    let uniqueNoBm = [...new Set(no_bm)];

    // Menampilkan informasi barang yang dipilih dalam modal (dengan no_bm yang unik)
    for (let i = 0; i < uniqueNoBm.length; i++) {
        let decodedNamaBarang = decodeURIComponent(uniqueNoBm[i].replace(/\+/g, ' ')); // Decode nama barang
        itemsHTML += `<p>${decodedNamaBarang}</p>`;
    }

    $('#barang1').html(itemsHTML); // Menampilkan semua item yang dipilih
    $('#jumlah_beli_container').html(''); // Clear previous entries

    // Dinamis menambahkan jumlah beli per barang
    for (let i = 0; i < jumlah.length; i++) {
        $('#jumlah_beli_container').append(`

            <div>
                <input type="hidden" id="ids_transaksi_${i}" value="${ids_transaksi[i]}" class="input-field" />
                <label for="jumlah_beli_${i}">${nama_barang[i]} (${satuan_beli[i]}) :</label>
                <input type="text" id="jumlah_beli_${i}" value="${jumlah[i]}" class="input-field" />
            </div>
        `);
    }

    my_modal_1.showModal(); // Menampilkan modal
}


function updateTransaksi1() {
    let jumlahBeli = [];
    let id = [];
    let status = $('#status').val();

    // Ambil nilai jumlah beli dari semua input yang telah ditampilkan
    $('input[id^="jumlah_beli_"]').each(function(index) {
        jumlahBeli.push($(this).val()); // Mengambil value dari setiap input
    });
    $('input[id^="ids_transaksi_"]').each(function(index) {
        id.push($(this).val()); // Mengambil value dari setiap input
    });

    // Log data ke console untuk debugging
    console.log("Jumlah Beli:", jumlahBeli);
    console.log("ID Transaksi:", id);
    console.log("Status:", status);

    // Validasi apakah jumlah beli dan status sudah diisi
    if (jumlahBeli.some(jumlah => !jumlah) || status === "") {
        alert("Silakan diisi status/keterangan. Contoh : Sesuai");
        return; // Hentikan eksekusi jika input kosong
    }

    if (confirm('Apakah Anda yakin?')) {
    let dataKirim = {
        id: id, // Array ID transaksi
        jumlah_beli: jumlahBeli, // Array jumlah beli
        stts: status, // Status transaksi
        _token: "{{ csrf_token() }}"
    };

    console.log("Data yang dikirim ke server:", dataKirim); // Debugging

    $.ajax({
        type: "PUT",
        url: "{{ route('transaksi.update1') }}",
        data: dataKirim,
        success: function(response) {
            console.log("Response dari server:", response);
            refreshTable();
            alert("Update Berhasil!");
            my_modal_1.close();
        },
        error: function(xhr, status, error) {
            console.log("Error:", xhr.responseText);
            alert("Update gagal! Cek console untuk detail.");
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
