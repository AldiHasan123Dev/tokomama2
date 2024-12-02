<x-Layout.layout>
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.2/css/dataTables.dateTime.min.css">
    <style>
        #table-ppn {
            font-size: 12px; /* Ukuran font kecil */
            width: 100%; /* Pastikan tabel memenuhi lebar */
        }
    
        #table-ppn th, #table-ppn td {
            padding: 4px; /* Kurangi padding */
        }
    
        #table-ppn_wrapper .dataTables_length, 
        #table-ppn_wrapper .dataTables_filter, 
        #table-ppn_wrapper .dataTables_info, 
        #table-ppn_wrapper .dataTables_paginate {
            font-size: 10px; /* Ukuran font lebih kecil untuk kontrol tabel */
        }
    
        #table-ppn_wrapper .dataTables_paginate .paginate_button {
            padding: 2px 5px; /* Kurangi padding tombol paginasi */
            font-size: 10px; /* Ukuran font tombol lebih kecil */
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
                <!-- Input Hidden for Start and End Date for Excel Export -->
                <input type="hidden" name="start" id="startex" value="{{ date('Y-m-d') }}" required>
                <input type="hidden" name="end" id="endex" value="{{ date('Y-m-d') }}" required>
                <button type="submit" class="btn w-28 font-semibold text-white bg-green-500 hover:bg-green-400" id="excel">Export Excel</button>
            </form>

            <form action="{{ route('pajak.export.ppncsv') }}" method="post">
                @csrf
                <!-- Input Hidden for Start and End Date for CSV Export -->
                <input type="hidden" name="start" id="startcs" value="{{ date('Y-m-d') }}" required>
                <input type="hidden" name="end" id="endcs" value="{{ date('Y-m-d') }}" required>
                <button type="submit" class="btn w-28 font-semibold text-white bg-blue-500 hover:bg-blue-400" id="csv">Export CSV</button>
            </form>
        </div>

        <hr>

        <div class="overflow-x-auto">
            <table border="0" cellspacing="5" cellpadding="5">
                <tbody>
                    <tr>
                        <td>Tanggal Mulai:</td>
                        <td><input type="text" id="min" name="min" class="rounded-md"></td>
                    </tr>
                    <tr>
                        <td>Tanggal Selesai:</td>
                        <td><input type="text" id="max" name="max" class="rounded-md"></td>
                    </tr>
                </tbody>
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
                <tbody>
                </tbody>
            </table>
        </div>
    </x-pajak.card>

    <x-slot:script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
        <script src="https://cdn.datatables.net/datetime/1.5.2/js/dataTables.dateTime.min.js"></script>
        <script>
            $(document).ready(function() {
                let minDate, maxDate;

                // Custom filter for date range
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    let min = minDate.val();
                    let max = maxDate.val();
                    let date = new Date(data[7]); // Assuming the date is in the 8th column (index 7)

                    if (
                        (min === null && max === null) ||
                        (min === null && date <= max) ||
                        (min <= date && max === null) ||
                        (min <= date && date <= max)
                    ) {
                        return true;
                    }
                    return false;
                });

                // Initialize date pickers
                minDate = new DateTime('#min', { format: 'YYYY-MM-DD' });
                maxDate = new DateTime('#max', { format: 'YYYY-MM-DD' });

                // Initialize DataTable
                let table = $('#table-ppn').DataTable({
                    pageLength: 20, // Batas data per halaman
                    lengthMenu: [20, 50, 100, 150], // Pilihan jumlah data per halaman
                    ordering: false,
                    scrollX: true, 
                    ajax: {
                        url: "{{ route('pajak.laporan-ppn.data') }}",
                        dataSrc: "data",
                    },
                    autoWidth: true,
                    columns: [
                        { data: 'DT_RowIndex', name: 'number', className: 'text-center' },
                        { data: 'invoice', name: 'invoice', className: 'text-center' },
                        { data: 'npwp', name: 'npwp', className: 'text-center' },
                        { data: 'nik', name: 'nik', className: 'text-center' },
                        { data: 'nama', name: 'nama', className: 'text-center' },
                        { data: 'nama_npwp', name: 'nama_npwp', className: 'text-center' },
                        { data: 'alamat_npwp', name: 'alamat_npwp', className: 'text-center' },
                        { data: 'tgl_invoice', name: 'tanggal_invoice', className: 'text-center' },
                        { data: 'tujuan', name: 'tujuan', className: 'text-center' },
                        { data: 'uraian', name: 'uraian', className: 'text-center' },
                        { data: 'faktur', name: 'faktur', className: 'text-center' },
                        { data: 'subtotal', name: 'subtotal', render: $.fn.dataTable.render.number('.', ',', 2) },
                        { data: 'ppn', name: 'ppn' },
                        { data: 'nominal_ppn', name: 'nominal_ppn', render: $.fn.dataTable.render.number('.', ',', 2) },
                        { data: 'total', name: 'total', render: $.fn.dataTable.render.number('.', ',', 2) },
                        { data: 'id', name: 'id', visible: false }
                    ]
                });

                // Refilter the table when date inputs change
                $('#min, #max').on('change', function() {
                    table.draw();
                });

                // Update export date values on change
                $("#min").on('change', function() {
                    var inputValue = $(this).val();
                    $('#startex').val(inputValue); // Set value for Excel export
                    $('#startcs').val(inputValue); // Set value for CSV export
                });

                $('#max').on('change', function() {
                    var inputValue = $(this).val();
                    $('#endex').val(inputValue); // Set value for Excel export
                    $('#endcs').val(inputValue); // Set value for CSV export
                });
                $("form").on('submit', function(event) {
            // Cek apakah input tanggal mulai dan selesai sudah diisi
            if ($('#min').val() === '' || $('#max').val() === '') {
                event.preventDefault(); // Batalkan pengiriman form
                alert('Silakan isi tanggal mulai dan tanggal selesai terlebih dahulu.');
            }
        });
            });
        </script>
    </x-slot:script>
</x-Layout.layout>
