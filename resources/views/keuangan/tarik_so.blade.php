<x-Layout.layout>
    <style>
        tr.selected {
            background-color: lightskyblue !important;
        }

        /* Beri ruang di dalam kolom Aksi */
        #table-getfaktur td[aria-describedby="table-getfaktur_aksi"] {
            padding: 0 4px !important;
            box-sizing: border-box;
            text-align: center;
        }

        /* Tombol Tarik SO */
        #table-getfaktur .btn-tarik-so {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 3px 7px;
            border: 0;
            border-radius: 4px;
            background-color: #c81414;
            color: #fff;
            font-size: 10px;
            line-height: 1.2 !important;
            white-space: nowrap;
            cursor: pointer;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Tarik SO</x-slot:tittle>

        <x-slot:button>
            <div class="flex gap-2">
                <div class="flex-gap-2">
                    <input type="hidden" name="invoice_count" id="count" value="1"
                        class="rounded-md form-control text-center" min="1" style="height: 28px">
                </div>
            </div>
        </x-slot:button>

        <div class="overflow-x-auto mt-5">
            <div class="table-responsive">
                <!-- Checkbox "Select All" di luar tabel -->
                <table class="table" id="table-getfaktur"></table>
                <div id="jqGridPager"></div>
            </div>
        </div>
        <div class="card border rounded-lg shadow-sm p-4">
            <div class="text-sm text-gray-500">
                Total Harga Beli
            </div>

            <div id="total-harga-beli" class="text-xl font-bold mt-2">
                0
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
                    url: "{{ route('invoice.pre-invoice1') }}", // Ganti dengan URL API yang benar
                    mtype: "GET",
                    datatype: "json",
                    colModel: [{
                            search: true,
                            name: 'DT_RowIndex',
                            index: 'DT_RowIndex',
                            label: 'No.',
                            width: 20,
                            align: 'center'
                        },
                        {
                            search: true,
                            name: 'draft_no',
                            index: 'draft_no',
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
                            name: 'harga_beli',
                            index: 'harga_beli',
                            label: 'Harga Beli',
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
                        {
                            search: true,
                            name: 'no_pol',
                            index: 'no_pol',
                            label: 'No. Pol',
                            align: 'center'
                        },
                        {
                            name: 'aksi',
                            index: 'aksi',
                            label: 'Aksi',
                            width: 72,
                            align: 'center',
                            sortable: false,
                            search: false,
                            formatter: function(cellValue, options, rowObject) {
                                const id = Number(options.rowId || rowObject.id);

                                if (!Number.isInteger(id) || id <= 0) {
                                    return '';
                                }

                                const draftNo = $('<div>').text(rowObject.draft_no || '-').html()
                                    .replace(/"/g, '&quot;');

                                return '<button type="button" class="btn-tarik-so" data-id="' + id +
                                    '" data-draft="' + draftNo + '">Tarik SO</button>';
                            }
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
                        let totalHargaBeli = 0;

                        (data.data || []).forEach(function(item) {
                            let sisa = parseFloat(item.sisa) || 0;
                            let hargaBeli = parseFloat(item.harga_beli) || 0;

                            let subtotalHargaBeli = hargaBeli * sisa;

                            totalHargaBeli += subtotalHargaBeli;
                        });

                        // Bulatkan hasil akhir
                        totalHargaBeli = Math.round(totalHargaBeli);

                        $('#total-harga-beli').text(
                            totalHargaBeli.toLocaleString('id-ID')
                        );

                        console.log('Total Harga Beli:', totalHargaBeli);
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
            });

            // Proses Tarik SO berdasarkan baris yang diklik
            $(document).on('click', '.btn-tarik-so', function() {
                const $btn = $(this);
                const id = $btn.data('id');
                const draftNo = $btn.data('draft') || '-';

                if (!id) {
                    alert('SO tidak ditemukan.');
                    return;
                }

                if (!confirm('Apakah kamu yakin menarik SO ini?\n\nNomor Surat: ' + draftNo)) {
                    return;
                }

                const url = "{{ route('invoice.hapus-forced', ':id') }}".replace(':id', id);

                $btn.prop('disabled', true).text('Proses...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(res) {
                        alert(res.message || 'SO Berhasil di tarik.');

                        // loadonce: true, jadi harus kembali ke datatype json agar data diambil ulang dari server
                        $("#table-getfaktur")
                            .jqGrid('setGridParam', {
                                datatype: 'json'
                            })
                            .trigger('reloadGrid');
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ?
                            xhr.responseJSON.message :
                            'Terjadi kesalahan saat menghapus data.';
                        alert(msg);
                        $btn.prop('disabled', false).text('Tarik SO');
                    }
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
