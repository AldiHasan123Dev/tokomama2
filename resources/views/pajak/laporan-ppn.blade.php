<x-Layout.layout>
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.2/css/dataTables.dateTime.min.css">
    <style>
        #table-ppn {
            font-size: 12px;
            width: 100%;
        }

        #table-ppn th, #table-ppn td {
            padding: 4px;
        }

        #table-ppn_wrapper .dataTables_length, 
        #table-ppn_wrapper .dataTables_filter, 
        #table-ppn_wrapper .dataTables_info, 
        #table-ppn_wrapper .dataTables_paginate {
            font-size: 10px;
        }

        #table-ppn_wrapper .dataTables_paginate .paginate_button {
            padding: 2px 5px;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }
    </style>

    <x-pajak.card>
        <x-slot:tittle>Laporan PPN</x-slot:tittle>

        <div class="grid grid-cols-7 gap-4 mt-4 mb-4">
            <form action="{{ route('pajak.export.ppnexc') }}" method="post">
                @csrf
                <input type="hidden" name="start" id="startex" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="end" id="endex" value="{{ date('Y-m-d') }}">
                <button type="submit" class="btn w-28 font-semibold text-white bg-green-500 hover:bg-green-400">Export Excel</button>
            </form>

            <form action="{{ route('pajak.export.ppncsv') }}" method="post">
                @csrf
                <input type="hidden" name="start" id="startcs" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="end" id="endcs" value="{{ date('Y-m-d') }}">
                <button type="submit" class="btn w-28 font-semibold text-white bg-blue-500 hover:bg-blue-400">Export CSV</button>
            </form>
        </div>

        <hr>

        <div class="overflow-x-auto">
            <table border="0" cellspacing="5" cellpadding="5">
                <tr>
                    <td>Tanggal Mulai:</td>
                    <td><input type="text" id="min" name="min" class="rounded-md"></td>
                </tr>
                <tr>
                    <td>Tanggal Selesai:</td>
                    <td><input type="text" id="max" name="max" class="rounded-md"></td>
                </tr>
            </table>

            <table class="cell-border hover nowrap" id="table-ppn">
                <thead>
                    <tr>
                        <th class="text-center">No.</th>
                        <th class="text-center">Invoice</th>
                        <th class="text-center">NPWP</th>
                        <th class="text-center">NIK</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">Nama NPWP</th>
                        <th class="text-center">Alamat NPWP</th>
                        <th class="text-center">Tanggal Faktur</th>
                        <th class="text-center">Tujuan</th>
                        <th class="text-center">Uraian</th>
                        <th class="text-center">Faktur</th>
                        <th class="text-center">Sub Total (Rp)</th>
                        <th class="text-center">PPN</th>
                        <th class="text-center">Nominal PPN (Rp)</th>
                        <th class="text-center">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white fw-bold">
                Ringkasan Total
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" style="min-width: 1000px;">
                        <tbody>
                            <tr>
                                <th colspan="14" class="text-end align-middle">Total (Rp):</th>
                                <td class="text-end fw-semibold" id="grand-total"></td>
                            </tr>
                            <tr>
                                <th colspan="14" class="text-end align-middle">Total Sub Total (Rp):</th>
                                <td class="text-end fw-semibold" id="grand-subtotal"></td>
                            </tr>
                            <tr>
                                <th colspan="14" class="text-end align-middle">Total Nominal PPN (Rp):</th>
                                <td class="text-end fw-semibold" id="grand-nominalPpn"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
                   
            
        </div>
    </x-pajak.card>

    <x-slot:script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
        <script src="https://cdn.datatables.net/datetime/1.5.2/js/dataTables.dateTime.min.js"></script>
        <script>
            $(document).ready(function() {
                let minDate = new DateTime('#min', { format: 'YYYY-MM-DD' });
                let maxDate = new DateTime('#max', { format: 'YYYY-MM-DD' });

                $.fn.dataTable.ext.search.push(function(settings, data) {
                    let min = minDate.val();
                    let max = maxDate.val();
                    let date = new Date(data[7]); // tanggal faktur

                    if ((min === null && max === null) ||
                        (min === null && date <= max) ||
                        (min <= date && max === null) ||
                        (min <= date && date <= max)) {
                        return true;
                    }
                    return false;
                });

                let table = $('#table-ppn').DataTable({
    pageLength: 20,
    lengthMenu: [20, 50, 100, 150],
    ordering: false,
    scrollX: true,
    scrollY: "400px",
    scrollCollapse: true,
    fixedHeader: {
        footer: true
    },
    ajax: {
        url: "{{ route('pajak.laporan-ppn.data') }}",
        dataSrc: "data",
    },
    columns: [
        { data: 'DT_RowIndex', className: 'text-center' },
        { data: 'invoice', className: 'text-center' },
        { data: 'npwp', className: 'text-center' },
        { data: 'nik', className: 'text-center' },
        { data: 'nama', className: 'text-center' },
        { data: 'nama_npwp', className: 'text-center' },
        { data: 'alamat_npwp', className: 'text-center' },
        { data: 'tgl_invoice', className: 'text-center' },
        { data: 'tujuan', className: 'text-center' },
        { data: 'uraian', className: 'text-center' },
        { data: 'faktur', className: 'text-center' },
        { data: 'subtotal', render: $.fn.dataTable.render.number('.', ',', 2), className: 'text-right' },
        { data: 'ppn', className: 'text-center' },
        { data: 'nominal_ppn', render: $.fn.dataTable.render.number('.', ',', 2), className: 'text-right' },
        { data: 'total', render: $.fn.dataTable.render.number('.', ',', 2), className: 'text-right' },
        { data: 'id', visible: false }
    ],
    footerCallback: function (row, data) {
        let api = this.api();

        // Helper function
        function getTotal(colIndex) {
            return api.column(colIndex, { page: 'current' }).data().reduce((a, b) => {
                let x = typeof a === 'string' ? parseFloat(a.replace(/[.]/g, '').replace(/,/g, '.')) || 0 : a;
                let y = typeof b === 'string' ? parseFloat(b.replace(/[.]/g, '').replace(/,/g, '.')) || 0 : b;
                return x + y;
            }, 0);
        }

        let subtotal = getTotal(11);       // kolom subtotal
        let nominal_ppn = getTotal(13);    // kolom nominal ppn
        let total = getTotal(14);          // kolom total

        $('#grand-subtotal').html(subtotal.toLocaleString('id-ID', { minimumFractionDigits: 2 }));
        $('#grand-nominalPpn').html(nominal_ppn.toLocaleString('id-ID', { minimumFractionDigits: 2 }));
        $('#grand-total').html(total.toLocaleString('id-ID', { minimumFractionDigits: 2 }));
    }
});


                $('#min, #max').on('change', function() {
                    table.draw();
                    $('#startex').val($('#min').val());
                    $('#startcs').val($('#min').val());
                    $('#endex').val($('#max').val());
                    $('#endcs').val($('#max').val());
                });

                $("form").on('submit', function(event) {
                    if (!$('#min').val() || !$('#max').val()) {
                        event.preventDefault();
                        alert('Silakan isi tanggal mulai dan tanggal selesai terlebih dahulu.');
                    }
                });
            });
        </script>
    </x-slot:script>
</x-Layout.layout>
