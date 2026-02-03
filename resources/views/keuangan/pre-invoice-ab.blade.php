<x-Layout.layout>
    <style>
        tr.selected {
            background-color: lightskyblue !important;
        }

        /* ================= MODAL BASE ================= */

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: none;
            /* 🔴 WAJIB */
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        /* aktif */
        .modal-overlay.active {
            display: flex;
        }

        /* ================= MODAL BOX ================= */

        .modal-box {
            width: 100%;
            max-width: 600px;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            animation: modalFade 0.25s ease;
        }

        /* HEADER */
        .modal-header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: #fff;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 18px;
            margin: 0;
        }

        .modal-close {
            font-size: 22px;
            cursor: pointer;
        }

        /* BODY */
        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }

        .form-group input {
            width: 100%;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 0 12px;
            background: #f9fafb;
        }

        /* FOOTER */
        .modal-footer {
            padding: 14px 20px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            border-top: 1px solid #e5e7eb;
        }

        /* BUTTON */
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-secondary {
            background: #e5e7eb;
        }

        /* ANIMATION */
        @keyframes modalFade {
            from {
                transform: scale(0.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* ===== jqGrid row reset ===== */
        /* ===== FIX tinggi baris jqGrid ===== */


        /* ===== wrapper penentu posisi ===== */
        .aksi-center {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }


        /* ===== tombol ===== */
        .btn-hapus-mob {
            height: 26px;
            padding: 0 14px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 4px;

            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-hapus-mob:hover {
            background-color: #b02a37;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


    <x-keuangan.card-keuangan>
        <x-slot:tittle>Pre-Invoice untuk Alat Berat</x-slot:tittle>
        <button type="button" id="btn-buat-order" class="btn font-semibold bg-orange-500 btn-sm text-white mt-4">
            Buat Order
        </button>
        <button type="submit" id="btn-order" class="btn font-semibold bg-blue-500 btn-sm text-white mt-4">Tambah
            Tagihan
        </button>
        <x-slot:button>
            <form action="{{ route('invoice-ab.form') }}" method="get" id="form">
                <input type="hidden" name="id" id="id">
                <div class="flex gap-2">
                    <div class="flex-gap-2">
                        <input type="hidden" name="invoice_count" id="count" value="1"
                            class="rounded-md form-control text-center" min="1" style="height: 28px">
                    </div>
                    <button type="submit" class="btn font-semibold bg-green-500 btn-sm text-white mt-4">
                        Buat Invoice Alat Berat
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
        <!-- Modal -->
        <!-- MODAL OVERLAY -->
        <div id="modal-action" class="modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h3>Tambah Tagihan</h3>
                    <span class="modal-close" data-target="modal-action">&times;</span>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="detail_id">

                    <div class="form-group">
                        <label>Nama Alat</label>
                        <input type="text" id="detail_nama_alat" readonly>
                    </div>

                    <div class="form-group">
                        <label>Tarif</label>
                        <input type="text" id="detail_tarif" readonly>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" id="detail_ket">
                    </div>

                    <div class="form-group">
                        <label>Nominal</label>
                        <input type="text" id="detail_nominal">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="simpan-detail-alat">Tambah Tagihan</button>
                    </div>
                </div>
            </div>
        </div>


        <div id="modal-order" class="modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h3>Buat Order</h3>
                    <span class="modal-close" data-target="modal-order">&times;</span>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Tarif / Alat Berat</label>
                        <select id="order_tarif" style="width:100%"></select>
                    </div>

                    <div class="form-group">
                        <label>Customer</label>
                        <select id="order_customer" style="width:100%"></select>
                    </div>
                    <div class="form-group">
                        <label>Barang</label>
                        <input type="text" id="order_barang" style="width:100%">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Order</label>
                        <input type="date" id="order_tanggal">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea id="order_keterangan" class="form-control" style="width:100%; height:50%"
                            placeholder="Masukkan keterangan tambahan (opsional)">
        </textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" id="buat-order">Proses</button>
                </div>
            </div>
        </div>



    </x-keuangan.card-keuangan>

    <div id="mob-wrapper" style="display:none; margin-top:20px;">
        <x-keuangan.card-keuangan>
            <x-slot:tittle>Detail Tambahan Tagihan</x-slot:tittle>
            <div class="table-responsive">
                <div class="overflow-x-auto mt-5">
                    <table id="table-mob"></table>
                    <div id="pager-mob"></div>
                </div>
            </div>

    </div>


    </x-keuangan.card-keuangan>

    <x-keuangan.card-keuangan>
        <button type="button" id="btn-cetak-invoice" class="btn font-semibold bg-green-600 btn-sm text-white mt-4">
            Cetak Invoice
        </button>
        <x-slot:tittle>Order sudah Invoice</x-slot:tittle>
        <div class="table-responsive">
            <div class="overflow-x-auto mt-5">
                <table id="table-invoice-ab"></table>
                <div id="pager-invoice-ab"></div>
            </div>
        </div>


    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
        <script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>

        <script>
            let selectedKodeInvoice = null;

            $("#table-invoice-ab").jqGrid({
                url: "{{ route('invoice-ab.list') }}",
                datatype: "json",
                mtype: "GET",
                colModel: [{
                        name: 'id',
                        hidden: true
                    },
                    {
                        name: 'kode_invoice',
                        label: 'Kode Invoice',
                        width: 150
                    },
                    {
                        name: 'nama_customer',
                        label: 'Customer',
                        width: 200
                    },
                    {
                        name: 'tgl_invoice',
                        label: 'Tanggal',
                        width: 120,
                        align: 'center'
                    }
                ],
                pager: "#pager-invoice-ab",
                rowNum: 20,
                viewrecords: true,
                autowidth: true,
                height: 'auto',

                onSelectRow: function(rowid) {
                    const rowData = $("#table-invoice-ab")
                        .jqGrid('getRowData', rowid);

                    // simpan KODE INVOICE
                    selectedKodeInvoice = rowData.kode_invoice || null;

                    // highlight baris terpilih
                    $("#table-invoice-ab tr").removeClass('selected');
                    $("#" + rowid).addClass('selected');
                },

                jsonReader: {
                    root: "data",
                    id: "id"
                }
            });

            $('#btn-cetak-invoice').on('click', function() {

                if (!selectedKodeInvoice) {
                    alert('Silakan pilih invoice terlebih dahulu');
                    return;
                }

                const url = "{{ route('invoice-ab.cetak') }}" +
                    "?kode_invoice=" + encodeURIComponent(selectedKodeInvoice);

                window.open(url, '_blank');
            });


            $(document).ready(function() {
                let table = $("#table-getfaktur").jqGrid({
                    url: "{{ route('invoice.pre-invoice-ab') }}",
                    mtype: "GET",
                    datatype: "json",
                    colModel: [{
                            name: 'checkbox',
                            index: 'checkbox',
                            label: 'Pilih',
                            width: 20,
                            align: 'center',
                            formatter: function() {
                                return '<input type="checkbox" class="row-checkbox" />';
                            }
                        },
                        {
                            name: 'customers_id',
                            index: 'customers_id',
                            hidden: true
                        },
                        {
                            search: true,
                            name: 'DT_RowIndex',
                            index: 'DT_RowIndex',
                            width: 20,
                            label: 'No.',
                            align: 'center'
                        },
                        {
                            search: true,
                            name: 'nama_alat',
                            index: 'nama_alat',
                            label: 'Nama Alat Berat',
                            align: 'center'
                        },
                        {
                            search: true,
                            name: 'nama_customers',
                            index: 'nama_customers',
                            label: 'Nama Customer',
                            align: 'center'
                        },
                        {
                            search: true,
                            name: 'alamat_customers',
                            index: 'alamat_customers',
                            label: 'Alamat Customer',
                            align: 'center'
                        },
                        {
                            search: true,
                            name: 'tarif',
                            index: 'tarif',
                            label: 'Tarif',
                            formatoptions: {
                                decimalPlaces: 2,
                                thousandsSeparator: ',',
                            },
                            align: 'right',
                            formatter: 'number'
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
                    loadonce: false,
                    serverPaging: true,
                    onSelectRow: function(rowid) {

                        // ambil data BARU
                        let rowData = $("#table-getfaktur").jqGrid('getRowData', rowid);

                        // proteksi (jqGrid kadang return kosong saat reload)
                        if (!rowData || !rowData.id) {
                            console.warn('Row data kosong', rowData);
                            return;
                        }

                        // simpan ke global
                        selectedRowData = rowData;

                        // baru dipakai
                        $('#id').val(rowData.id);

                        initMobGrid(rowData.id);
                    },


                    loadComplete: function(data) {},
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

                // Menghandle checkbox "Select All"1
                $('#select-all').change(function() {
                    var checked = $(this).is(':checked');
                    $('.row-checkbox').prop('checked', checked);
                });
            });

            $('#form').on('submit', function(e) {
                e.preventDefault();

                let selectedRows = [];
                let customerSet = new Set();

                // 🔥 AMBIL LANGSUNG CHECKBOX YANG DICENTANG
                let checkedBoxes = $('#table-getfaktur .row-checkbox:checked');

                console.log('Checkbox tercentang:', checkedBoxes.length);

                // ❌ TIDAK ADA YANG DIPILIH
                if (checkedBoxes.length === 0) {
                    console.warn('Tidak ada order yang dipilih');
                    alert('Silakan pilih order terlebih dahulu');
                    return;
                }

                checkedBoxes.each(function() {
                    let rowId = $(this).closest('tr.jqgrow').attr('id');
                    let rowData = $("#table-getfaktur").jqGrid('getRowData', rowId);

                    console.log('Row dipilih:', {
                        id: rowData.id,
                        customers_id: rowData.customers_id,
                        nama: rowData.nama_customers
                    });

                    selectedRows.push(rowData.id);
                    customerSet.add(rowData.customers_id);
                });

                // ❌ CUSTOMER BERBEDA
                if (customerSet.size > 1) {
                    console.error('VALIDASI GAGAL: customer berbeda');
                    alert('❌ Tidak bisa membuat invoice.\nOrder yang dipilih memiliki customer yang berbeda.');
                    return;
                }

                console.log('VALIDASI LOLOS ✅');
                console.log('Customer dipakai:', [...customerSet][0]);

                // kirim ke backend
                $('#id').val(selectedRows);
                console.log('Submit form dengan ID:', selectedRows);

                this.submit();
            });




            let selectedRowData = null;

            $('#btn-order').on('click', function() {

                if (!selectedRowData) {
                    alert('Silakan pilih data di tabel terlebih dahulu');
                    return;
                }

                $('#detail_id').val(selectedRowData.id);
                $('#detail_nama_alat').val(selectedRowData.nama_alat);
                $('#detail_tarif').val(selectedRowData.tarif);

                $('#modal-action').addClass('active');
            });

            // tutup modal
            $(document).on('click', '.modal-close', function() {
                $('#modal-action').removeClass('active');
            });

            $('#btn-buat-order').on('click', function() {
                $('#modal-order').addClass('active');
            });

            $(document).on('click', '.modal-close', function() {
                $('#modal-order').removeClass('active');
            });


            $('#order_tarif').select2({
                placeholder: 'Pilih Tarif / Alat Berat',
                dropdownParent: $('#modal-order'),
                ajax: {
                    url: "{{ route('tarif-ab.select2') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.nama_alat + ' - ' + item.tarif
                            }))
                        };
                    }
                }
            });

            $('#order_customer').select2({
                placeholder: 'Pilih Customer',
                dropdownParent: $('#modal-order'),
                ajax: {
                    url: "{{ route('customer.select2') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.nama_customer
                            }))
                        };
                    }
                }
            });

            $('#buat-order').on('click', function() {

                let tarif = $('#order_tarif').val();
                let customer = $('#order_customer').val();
                let tanggal = $('#order_tanggal').val();
                let keterangan = $('#order_keterangan').val();
                let barang = $('#order_barang').val();
                if (!tarif || !customer || !tanggal) {
                    alert('Semua field wajib diisi');
                    return;
                }

                $.ajax({
                    url: "{{ route('order-ab.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tarif_id: tarif,
                        customer_id: customer,
                        tanggal: tanggal,
                        keterangan: keterangan,
                        barang: barang
                    },
                    success: function(res) {
                        if (res.status) {
                            alert(res.message);
                            $('#order_tarif').val(null).trigger('change');
                            $('#order_customer').val(null).trigger('change');
                            $('#order_tanggal').val('');
                            $('#order_keterangan').val('');
                            $('#order_barang').val('');
                            $('#modal-order').removeClass('active');
                            $("#table-getfaktur").trigger('reloadGrid');
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan saat menyimpan data');
                    }
                });
            });
            $('#simpan-detail-alat').on('click', function() {

                let id = $('#detail_id').val();
                let ket = $('#detail_ket').val();
                let nominal = $('#detail_nominal').val();

                if (!ket || !nominal) {
                    alert('Mob dan Nominal wajib diisi');
                    return;
                }

                $.ajax({
                    url: "{{ route('tambah-tagihan') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id_order: id,
                        keterangan: ket,
                        nominal: nominal
                    },
                    success: function(res) {
                        if (res.status) {
                            alert(res.message);

                            // reset form modal
                            $('#detail_ket').val('');
                            $('#detail_nominal').val('');

                            $('#modal-action').removeClass('active');
                            $("#table-getfaktur").trigger('reloadGrid');
                            $("#table-mob").trigger('reloadGrid');
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Gagal menyimpan data');
                    }
                });
            });

            let mobGridInitialized = false;

            function initMobGrid(orderId) {

                if (!mobGridInitialized) {
                    $("#table-mob").jqGrid({
                        url: "{{ route('mob.by-order') }}",
                        mtype: "GET",
                        datatype: "json",
                        postData: {
                            order_id: orderId
                        },
                        colModel: [{
                                search: true,
                                name: 'id',
                                width: 20,
                                label: 'ID',
                                align: 'center'
                            },
                            {
                                label: 'Keterangan',
                                witdh: 50,
                                name: 'ket'
                            },
                            {
                                label: 'Nominal',
                                name: 'nominal',
                                width: 20,
                                align: 'right',
                                formatter: 'number'
                            },
                            {
                                label: 'Aksi',
                                name: 'aksi',
                                width: 20,
                                align: 'center',
                                sortable: false,
                                search: false,
                                formatter: function(cellvalue, options, rowObject) {
                                    return `
        <button 
            type="button"
            class="btn btn-sm btn-danger btn-hapus-mob"
            data-id="${rowObject.id}"
            title="Hapus">
            <i class="bi bi-trash"></i>
        </button>
    `;
                                }

                            }

                        ],

                        pager: "#pager-mob",
                        rowNum: 20,
                        rowList: [10, 20, 50],
                        viewrecords: true,
                        autowidth: true,
                        height: 'auto',
                        loadonce: false,
                        serverPaging: true,

                        jsonReader: {
                            root: "data"
                        },

                        loadComplete: function(res) {

                            if (res.data.length === 0) {
                                $('#mob-wrapper').hide();
                            } else {
                                $('#mob-wrapper').show();

                                // 🔥 FIX LEBAR GRID
                                let parentWidth = $('#mob-wrapper').width();
                                $("#table-mob").jqGrid('setGridWidth', parentWidth);
                            }
                        }
                    });

                    mobGridInitialized = true;
                } else {


                    // 🔥 WAJIB: kosongkan data lama
                    $("#table-mob").jqGrid('clearGridData', true);

                    $("#table-mob")
                        .jqGrid('setGridParam', {
                            postData: {
                                order_id: orderId
                            },
                            page: 1,
                            datatype: 'json'
                        })
                        .trigger('reloadGrid');

                    let parentWidth = $('#mob-wrapper').width();
                    $("#table-mob").jqGrid('setGridWidth', parentWidth);

                }
            }
            $(document).on('click', '.btn-hapus-mob', function() {
                let id = $(this).data('id');

                if (!id) {
                    alert('ID tidak ditemukan');
                    return;
                }

                if (!confirm('Yakin ingin menghapus data ini?')) return;

                $.ajax({
                    url: "{{ route('mob.destroy') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    success: function() {
                        $("#table-mob").trigger('reloadGrid');
                    },
                    error: function() {
                        alert('Gagal menghapus data');
                    }
                });
            });

            $('#form').on('submit', function(e) {
                let id = $('#id').val();

                if (!id) {
                    e.preventDefault();
                    alert('Silakan pilih order terlebih dahulu');
                    return false;
                }
            });
        </script>
    </x-slot:script>
</x-Layout.layout>
