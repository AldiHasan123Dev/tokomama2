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
#table-mob tr.jqgrow td {
    padding: 0 !important;
    vertical-align: middle !important;
}

/* ===== wrapper penentu posisi ===== */
.aksi-center {
    display: flex;
    align-items: center;        /* center vertikal */
    justify-content: center;    /* center horizontal */
    height: 100%;
}

/* ===== tombol ===== */
.btn-hapus-mob {
    display: flex;
    align-items: center;        /* teks center */
    justify-content: center;

    height: 26px;
    padding: 0 14px;

    background-color: #dc3545;
    color: #fff;
    border: none;
    border-radius: 4px;

    font-size: 12px;
    font-weight: 500;
    line-height: normal;        /* 🔴 JANGAN diubah */
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
                    <button type="button" class="btn font-semibold bg-green-500 btn-sm text-white mt-4">Buat
                        Invoice Alat Berat</button>
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
                    <h3>Detail Order</h3>
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
                        <label>Tanggal Order</label>
                        <input type="date" id="order_tanggal">
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
            <x-slot:tittle>Detail MOB</x-slot:tittle>
            <div class="table-responsive">
                <div class="overflow-x-auto mt-5">
                    <table id="table-mob"></table>
                    <div id="pager-mob"></div>
                </div>
            </div>

    </div>

    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
        <script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>

        <script>
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

                        $("#table-getfaktur tr").removeClass("selected");
                        $("#" + rowid).addClass("selected");

                        selectedRowData = $("#table-getfaktur").jqGrid('getRowData', rowid);

                        // 🔥 PANGGIL GRID MOB
                        initMobGrid(selectedRowData.id);
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

                // Menghandle checkbox "Select All"
                $('#select-all').change(function() {
                    var checked = $(this).is(':checked');
                    $('.row-checkbox').prop('checked', checked);
                });
            });

            $('#form').submit(function(e) {
                e.preventDefault();
                var ids = $("#table-getfaktur input:checkbox:checked").map(function() {
                    return $(this).closest('tr').find('td:last-child')
                        .text();
                }).get();
                if (ids.length === 0) {
                    alert('Silakan pilih item data terlebih dahulu.');
                } else {
                    $('#id').val(ids);
                    this.submit();
                }
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
                        tanggal: tanggal
                    },
                    success: function(res) {
                        if (res.status) {
                            alert(res.message);
                            $('#order_tarif').val(null).trigger('change');
                            $('#order_customer').val(null).trigger('change');
                            $('#order_tanggal').val('');
                            $('#modal-order').removeClass('active');
                            $("#table-getfaktur").trigger('reloadGrid');
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan saat menyimpan data');
                        console.error(xhr.responseText);
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
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Gagal menyimpan data');
                        console.error(xhr.responseText);
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
                            name: 'DT_RowIndex',
                            index: 'DT_RowIndex',
                            width: 20,
                            label: 'No.',
                            align: 'center'
                            },
                            {
                                label: 'Keterangan',
                                witdh: 80,
                                name: 'ket'
                            },
                            {
                                label: 'Nominal',
                                name: 'nominal',
                                width: 80,
                                align: 'right',
                                formatter: 'number'
                            },
                            {
                                label: 'Aksi',
                                name: 'aksi',
                                width: 30,
                                align: 'center',
                                
                                sortable: false,
                                formatter: function (cellValue, options, rowObject) {
                                    return `
                                        <div class="aksi-center">
                                            <button type="button"
                                                class="btn-hapus-mob"
                                                onclick="hapusMob(${rowObject.id})">
                                                Hapus
                                            </button>
                                        </div>
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
            postData: { order_id: orderId },
            page: 1,
            datatype: 'json'
        })
        .trigger('reloadGrid');

    let parentWidth = $('#mob-wrapper').width();
    $("#table-mob").jqGrid('setGridWidth', parentWidth);

                }

                function hapusMob(id) {
                    if (!confirm('Yakin ingin menghapus data ini?')) return;

                    $.ajax({
                        url: "{{ route('mob.destroy') }}", // 🔴 sesuaikan route
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function(res) {

                            // Reload grid
                            $("#table-mob").jqGrid('setGridParam', {
                                datatype: 'json'
                            }).trigger('reloadGrid');

                        },
                        error: function() {
                            alert('Gagal menghapus data');
                        }
                    });
                }
            }
        </script>
    </x-slot:script>
</x-Layout.layout>
