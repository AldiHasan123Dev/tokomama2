<x-Layout.layout>
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
            border-radius: 4px;
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

        .input-field,
        .select-field {
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
            background-color: #e0a50f;
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
    <dialog id="my_modal_5" class="modal">
        <div class="modal-box w-11/12 max-w-2xl pl-10">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-lg font-bold">Input Harga: <span id="barang"></span></h3>
            <label class="form-control w-full max-w">
                <div class="form-label">
                    <span class="form-label">Harga Beli</span>
                </div>
                <input type="text" class="input-field" id="harga_beli" oninput="formatRibuan(this)"
                    onclick="this.select()" />
            </label>
            {{-- <label class="form-control w-full max-w">
                <div class="form-label">
                    <span class="form-label">Profit</span>
                </div>
                <input type="text" class="input-field" readonly id="profit" />
            </label> --}}
            <button type="button" class="submit-button" onclick="updateTransaksi()">Update</button>
        </div>
    </dialog>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/css/ui.jqgrid.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/jquery.jqgrid.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>List Belum Input Harga Beli <span style="color: red;">(Khusus HARGA BELI yang ada PPN, isikan sampai 4 angka dibelakang koma)</span></x-slot:tittle>
        <div class="overflow-x-auto">
            <table id="table-non-tarif" class="table"></table>
            <div id="pager-non-tarif"></div>
        </div>
    </x-keuangan.card-keuangan>

    <x-keuangan.card-keuangan class="mt-3">
        <x-slot:tittle>List Sudah Input Harga <span style="color: red;">(Warna merah adalah angka di belakang koma)</span></x-slot:tittle>
        <div class="overflow-x-auto">
            <table class="table" id="table-tarif"></table>
            <div id="pager-tarif"></div>
        </div>
    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script>
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


            let id = null;
            let jumlah = 0;
            let table1;
            // Table Tarif
            $(document).ready(function() {
                table1 = $("#table-tarif").jqGrid({
                    url: "{{ route('transaksi.data1') }}",
                    datatype: "json",
                    mtype: "POST",
                    postData: {
                        _token: "{{ csrf_token() }}",
                        tarif: 1
                    },
                    colModel: [{
                            search: true,
                            label: 'Aksi',
                            name: 'aksi',
                            width: 150,
                            sortable: false,
                            align: 'center',
                            formatter: 'unformat'
                        },
                        {
                            search: true,
                            label: 'Tgl BM',
                            name: 'tgl_bm',
                            width: 150,
                            align: 'center'
                        },
                        {
                            search: true,
                            label: 'No. BM',
                            name: 'no_bm',
                            width: 150,
                            align: 'center'
                        },
                        {
                            search: true,
                            label: 'Barang',
                            name: 'barang',
                            width: 150
                        },
                        {
                            search: true,
                            label: 'Jumlah Beli',
                            name: 'jumlah_beli',
                            width: 150,
                            align: 'right'
                        },
                        {
                            search: true,
                            label: 'Satuan Beli',
                            name: 'satuan_beli',
                            width: 150,
                            align: 'center'
                        },
                        {
                            search: true,
                            label: 'Harga Beli',
                            name: 'harga_beli',
                            width: 150,
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
                            label: 'ID',
                            name: 'id',
                            width: 40,
                            hidden: true
                        }
                    ],
                    pager: "#pager-tarif",
                    rowNum: 10, // Jumlah baris per halaman
                    rowList: [10, 50, 100], // Opsi untuk memilih jumlah baris per halaman
                    viewrecords: true, // Tampilkan total record di footer
                    autowidth: true, // Menyesuaikan lebar otomatis
                    height: 'auto',
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
                        console.log("Data loaded for tarif:", data);
                        $("#table-tarif").jqGrid('filterToolbar');
                    }
                }).navGrid('#pager-tarif', {
                    edit: false,
                    add: false,
                    del: false,
                    search: false,
                    refresh: true,
                }, {}, {}, {}, {
                    closeAfterSearch: true,
                    closeAfterReset: true,
                    searchOnEnter: true,
                });
            });

            // Table Non-Tarif
            let table2 = $("#table-non-tarif").jqGrid({
                url: "{{ route('transaksi.data1') }}",
                datatype: "json",
                mtype: "POST",
                postData: {
                    _token: "{{ csrf_token() }}",
                    non_tarif: 1
                },
                colModel: [{
                        label: 'Aksi',
                        name: 'aksi',
                        width: 90,
                        sortable: false,
                        formatter: 'unformat'
                    },
                    {
                            search: true,
                            label: 'Tgl BM',
                            name: 'tgl_bm',
                            width: 150,
                            align: 'center'
                        },
                    {
                        label: 'No BM',
                        name: 'no_bm',
                        width: 70
                    },
                    {
                        label: 'Barang',
                        name: 'barang',
                        width: 150
                    },
                    {
                        label: 'Jumlah Beli',
                        name: 'jumlah_beli',
                        width: 70
                    },
                    {
                        label: 'Satuan Beli',
                        name: 'satuan_beli',
                        width: 30
                    },
                    {
                        label: 'Harga Beli',
                        name: 'harga_beli',
                        width: 70,
                        align: 'right'
                    },
                    {
                        label: 'ID',
                        name: 'id',
                        width: 40,
                        hidden: true
                    }
                ],
                pager: "#pager-non-tarif",
                rowNum: 20, // Jumlah baris per halaman
                rowList: [10, 20, 30, 50], // Opsi untuk memilih jumlah baris per halaman
                viewrecords: true, // Tampilkan total record di footer
                autowidth: true, // Menyesuaikan lebar otomatis
                height: 'auto',
                jsonReader: {
                    repeatitems: false,
                    root: "data",
                    page: "current_page",
                    total: "last_page",
                    records: "total"
                },
                loadComplete: function(data) {
                    console.log("Data loaded for non-tarif:", data);
                }
            }).navGrid('#pager-non-tarif', {
                edit: false,
                add: false,
                del: false,
                search: false,
                refresh: true,
            }, {}, {}, {}, {
                closeAfterSearch: true,
                closeAfterReset: true,
                searchOnEnter: true,
            });


            function inputTarif(id_transaksi, jual, beli, margin, qty, nama_barang, satuan_beli) {
                id = id_transaksi;
                jumlah = qty;
                $('#harga_jual').val(jual);
                $('#harga_beli').val(beli);
                $('#profit').val(margin);
                nama_barang = nama_barang.replace(/\+/g, ' ').replace(/%40/g, '@')
                    .trim(); // Menghapus '+' dan mengganti '%40' dengan '@'

                // Mengatur innerHTML
                document.getElementById('barang').innerHTML = `${nama_barang} (Harga PER - ${satuan_beli})`;
                my_modal_5.showModal();
            }

            function calculateProfit() {
                const jual = getCleanNumber($('#harga_jual').val()) * jumlah;
                const beli = getCleanNumber($('#harga_beli').val()) * jumlah;
                const margin = jual - beli;
                console.log("Harga Jual:", jual);
                console.log("Harga Beli:", beli);
                console.log("Profit (Margin):", margin);
                $('#profit').val(margin);
                formatRibuan(document.getElementById('profit'));
            }

            $('#harga_jual, #harga_beli').on('input', calculateProfit);

            function refreshTable() {
                if (typeof table1 !== 'undefined') {
                    table1.setGridParam({
                        datatype: 'json'
                    }).trigger("reloadGrid");
                }
                if (typeof table2 !== 'undefined') {
                    table2.setGridParam({
                        datatype: 'json'
                    }).trigger("reloadGrid");
                }
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

            $(document).ready(function() {
                // Jika ingin memformat profit saat halaman dimuat
                formatRibuan(document.getElementById('profit'));
            });
        </script>
    </x-slot:script>
</x-Layout.layout>
