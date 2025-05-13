<x-Layout.layout>
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/free-jqgrid@4.15.5/css/ui.jqgrid.min.css" />

    <!-- JS jQuery + jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!-- JS jqGrid -->
    <script src="https://cdn.jsdelivr.net/npm/free-jqgrid@4.15.5/js/jquery.jqgrid.min.js"></script>

    <x-keuangan.card-keuangan>
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
                background-color: #0ca065;
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
        <x-slot:tittle>Verifikasi dan Penjurnalan</x-slot:tittle>

        <div class="grid grid-cols-2 gap-2 justify-items-start mt-4 mb-4">
            <label class="form-control w-full max-w-xs mb-1">
                <div class="label">
                    <span class="label-text">Cari Tanggal Bayar</span>
                </div>
                <input type="date"
                    class="input input-sm input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                    id="tanggal_bayar1" name="tanggal_bayar1" autocomplete="off" value="{{ date('Y-m-d') }}" />
            </label>
            <button id="btn-jurnalkan" class="btn font-semibold bg-green-500 btn-sm text-white">Jurnalkan</button>
        </div>
        <table id="biayaGrid"></table>
        <div id="biayaPager"></div>
    </x-keuangan.card-keuangan>

    <!-- Modal -->
    <dialog id="jurnal-modal" class="modal">
        <div class="modal-box w-full max-w-2xl px-6 py-4">
            <!-- Tombol Close -->
            <form method="dialog">
                <button type="button" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                    onclick="document.getElementById('jurnal-modal').close()">
                    ✕
                </button>
            </form>

            <!-- Form Jurnal -->
            <form id="form-jurnal" method="POST" action="{{ route('keuangan.jurnal-inv') }}">
                @csrf

                <!-- Header -->
                {{-- <h3 class="text-xl font-bold mb-4">
                    Invoice: <span id="jurnal-invoice-text" class="font-semibold text-blue-600"></span>
                    Nilai Inv <span id="id-bayar" class="font-semibold text-blue-600"></span>
                </h3> --}}

                <h3 class="text-xl font-bold mb-4">
                    No Jurnal: <span class="font-semibold text-blue-600">{{ $no_BBM }}/BBM-TM/{{ date('y') }}</span>
                </h3>

                <!-- Hidden Inputs -->
                <input type="hidden" name="id[]" id="id-biaya">
                <input type="hidden" name="nomor" value="{{ $no_BBM }}/BBM-TM/{{ date('y') }}">
                <input type="hidden" name="no" value="{{ $no_BBM }}">
                <input type="hidden" name="id_transaksi[]" id="id-trans">
                <input type="hidden" name="invoice[]" id="invoice">
                <input type="hidden" name="customer[]" id="customer">
                <!-- Info Jurnal -->
                <div class="space-y-3 mb-4 ">
                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700">No Invoice:</label>
                        <div class="text-base font-semibold input-field text-black">{{ $noBBM->nomor }}</div>
                    </div> --}}

                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 text-center">Nominal terbayar:</label>
                        <ul id="nominal-id" class="list-disc input-field m-1"></ul>
                    </div> --}}
                </div>

                <!-- Input Tanggal -->
                <div class="mb-4">
                    <label for="jurnal-date" class="block text-sm font-medium text-gray-700 mb-1">
                        Pilih Tanggal Jurnal
                    </label>
                    <input type="date" name="tanggal" id="jurnal-date" class="input-field" required>
                </div>

                <!-- Tombol Submit -->
                <div class="modal-action">
                    <button type="submit" class="submit-button">Buat Jurnal</button>
                </div>
            </form>
        </div>
    </dialog>
    <script>
        $('#btn-jurnalkan').on('click', function() {
            let tanggal = $('#tanggal_bayar1').val();

            if (!tanggal) {
                alert("Silakan pilih tanggal bayar terlebih dahulu.");
                return;
            }

            $.ajax({
                url: '/keuangan/cari-transaksi-by-tanggal',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    tanggal_bayar: tanggal
                },
                success: function(response) {
                    let data = response.data;

                    if (!data || data.id.length === 0) {
                        alert("Tidak ada data transaksi di tanggal tersebut.");
                        return;
                    }

                    // Clear existing list in modal
                    $('#nominal-id').empty();
                    $('#customer-id').empty();

                    // Loop through the data and append to modal
                    data.id.forEach(function(item, index) {
                        let id = data.id[index];
                        let id_trans = data.id_transaksi[index];
                        let invoice = data.invoice[index];
                        let nominal = data.nominal[index];
                        let customer = data.customer[index];
                        $('#form-jurnal').append(
                            `<input type="hidden" name="id[]" value="${id}">`);
                        $('#form-jurnal').append(
                            `<input type="hidden" name="id_transaksi[]" value="${id_trans}">`
                            );
                        $('#form-jurnal').append(
                            `<input type="hidden" name="invoice[]" value="${invoice}">`);
                        $('#form-jurnal').append(
                            `<input type="hidden" name="customer[]" value="${customer}">`);

                        // Add data to modal dynamically
                        $('#nominal-id').append(`<div class= "input-field">
                            <span class="text-left"> ${invoice}</span> ||
                            <span class="text-right">${parseInt(nominal).toLocaleString()} || ${customer}</span>
                            </div>`);
                    });

                    // Display the first data's invoice in the modal header
                    // let firstInvoice = data.invoice[0];
                    // let firstNominal = data.nominal[0];
                    // $('#jurnal-invoice-text').text(firstInvoice);
                    // $('#id-bayar').text(parseInt(firstNominal).toLocaleString());

                    // Ganti action form untuk data pertama
                    let form = $('#form-jurnal');
                    let action = form.attr('action').replace('__REPLACE__', data.id[0]);
                    form.attr('action', action);

                    // Tampilkan modal <dialog>
                    const dialog = document.getElementById('jurnal-modal');
                    if (dialog) dialog.showModal();
                    else alert("Dialog modal tidak ditemukan di halaman.");
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. " + (xhr.responseJSON?.message || 'Coba lagi nanti.'));
                }
            });
        });
    </script>

    <!-- Script -->
    <script>
        $(document).ready(function() {
            // Reload grid saat filter tanggal berubah
            $('#tanggal_bayar1').on('change', function() {
                $("#biayaGrid").jqGrid('setGridParam', {
                    datatype: 'json',
                    postData: {
                        tgl_pembayar: $(this).val()
                    },
                    page: 1
                }).trigger('reloadGrid');
            });

            function resizeGrid() {
                $("#biayaGrid").setGridWidth($('#biayaGrid').closest(".ui-jqgrid").parent().width(), true);
            }
            $("#biayaGrid").jqGrid({
                url: '{{ route('biaya.monitoring.data') }}',
                datatype: "json",
                mtype: "GET",
                postData: {
                    tgl_pembayar: function() {
                        return $('#tanggal_bayar1').val();
                    }
                },
                colModel: [{
                    search: true,
                        label: 'No Jurnal',
                        name: 'jurnal',
                        align: 'center',
                        width: 150
                    },
                    {
                        search: true,
                        label: 'Tanggal Masuk Rekening',
                        name: 'tgl_pembayar',
                        align: 'center',
                        width: 100,
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'd/m/Y'
                        }
                    },
                    {
                        search: true,
                        label: 'Customer',
                        name: 'customer',
                        width: 150
                    },
                    {
                        search: true,
                        label: 'Invoice',
                        name: 'invoice',
                        width: 120
                    },
                    { search: true,
                        label: 'Nominal',
                        name: 'bayar',
                        width: 120,
                        align: 'right',
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ".",
                            decimalSeparator: ",",
                            decimalPlaces: 0,
                        },
                        summaryType: 'sum'
                    }
                    //                 ,
                    //                 {
                    //             label: 'Aksi',
                    //             name: 'aksi',
                    //             width: 100,
                    //             align: 'center',
                    //             sortable: false,
                    //             formatter: function (cellValue, options, rowObject) {
                    //                 const id = JSON.stringify(rowObject.id);
                    // const invoice = rowObject.invoice ?? '';
                    // const bayar = rowObject.bayar ?? '';
                    // const customer = rowObject.customer ?? '';

                    // // Ubah string "200000,300000" jadi array
                    // const list_nominal_array = rowObject.list_nominal?.split(',') ?? [];

                    //                 return `<button class="bg-green-500 hover:bg-red-300 m-1 text-white font-semibold py-1 px-2 rounded btn-sucsess" 
                //                  onclick='openJurnalModal(${id}, "${invoice}", ${JSON.stringify(list_nominal_array)}, "${customer}", "${bayar}")'>Jurnalkan</button>`;
                    //             }
                    //         }

                ],
                jsonReader: {
                    root: "rows",
                    page: "page",
                    total: "total",
                    records: "records",
                    repeatitems: false,
                    id: "id",
                    userdata: "userdata"
                },
                pager: "#biayaPager",
                rowNum: 10,
                rowList: [10, 20, 50],
                height: '100%',
                autowidth: true,
                shrinkToFit: true,
                viewrecords: true,
                footerrow: true,
                userDataOnFooter: true,
                caption: "Data Pembayaran Invoice",
                loadComplete: resizeGrid
            });
              $("#biayaGrid").jqGrid('filterToolbar', {
        searchOperators: false,
        searchOnEnter: false,
        defaultSearch: "cn"
    });

            $(window).on('resize', resizeGrid);
        });

        // Modal buka
        function openJurnalModal(idArray, invoice, listNominalArray, customer, bayar) {
            const formatted1 = new Intl.NumberFormat('id-ID').format(Number(bayar));
            console.log(bayar, formatted1);
            $('#jurnal-id').val(Array.isArray(idArray) ? idArray.join(', ') : idArray ?? '');
            $('#jurnal-invoice').val(invoice ?? '');
            $('#id-bayar').val(formatted1 ?? '');
            $('#jurnal-date').val('');
            $('#jurnal-invoice-text').text(invoice ?? '-');
            $('#customer-id').text(customer ?? '-');

            // Kosongkan isi dulu
            $('#nominal-id').empty();

            // Looping nominal dan tambahkan ke <ul>
            if (Array.isArray(listNominalArray)) {
                listNominalArray.forEach(nominal => {
                    const formatted = new Intl.NumberFormat('id-ID').format(Number(nominal));
                    $('#nominal-id').append(`<div class="input-field">${formatted}</div>`);
                });

            } else {
                $('#nominal-id').append(`<li>${listNominalArray ?? '-'}</li>`);
            }
            console.log(listNominalArray, idArray);

            document.getElementById('jurnal-modal').showModal();
        }



        // Tombol hapus jurnal
        // $(document).on('click', '.btn-sucsess', function () {
        //     const id = $(this).data('id');
        //     if (confirm('Yakin ingin menghapus data ini?')) {
        //         $.ajax({
        //             url: `/keuangan/jurnal/${id}`,
        //             type: 'POST',
        //             data: {
        //                 _token: '{{ csrf_token() }}'
        //             },
        //             success: function () {
        //                 alert('Data berhasil dihapus!');
        //                 $("#biayaGrid").trigger('reloadGrid');
        //             },
        //             error: function () {
        //                 alert('Terjadi kesalahan saat menghapus data.');
        //             }
        //         });
        //     }
        // });
    </script>
</x-Layout.layout>
