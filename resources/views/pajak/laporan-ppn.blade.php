<x-Layout.layout>
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.2/css/dataTables.dateTime.min.css">

    <x-pajak.card>
        <x-slot:tittle>Laporan PPN</x-slot:tittle>
        <div class="grid grid-cols-7 gap-4">
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
                        <th>No.</th>
                        <th>Invoice</th>
                        <th>NPWP</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Nama NPWP</th>
                        <th>Alamat NPWP</th>
                        <th>Tanggal Faktur</th>
                        <th>Tujuan</th>
                        <th>Uraian</th>
                        <th>Faktur</th>
                        <th>Sub Total (Rp)</th>
                        <th>PPN</th>
                        <th>Nominal PPN (Rp)</th>
                        <th>Total (Rp)</th>
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
                    pageLength: 100,
                    ajax: {
                        url: "{{ route('pajak.laporan-ppn.data') }}",
                        dataSrc: "data",
                    },
                    autoWidth: false,
                    columns: [
                        { data: 'DT_RowIndex', name: 'number' },
                        { data: 'invoice', name: 'invoice' },
                        { data: 'npwp', name: 'npwp' },
                        { data: 'nik', name: 'nik' },
                        { data: 'nama', name: 'nama' },
                        { data: 'nama_npwp', name: 'nama_npwp' },
                        { data: 'alamat_npwp', name: 'alamat_npwp' },
                        { data: 'tgl_invoice', name: 'tanggal_invoice' },
                        { data: 'tujuan', name: 'tujuan' },
                        { data: 'uraian', name: 'uraian' },
                        { data: 'faktur', name: 'faktur' },
                        { data: 'subtotal', name: 'subtotal', render: $.fn.dataTable.render.number('.', ',', 2) },
                        { data: 'ppn', name: 'ppn' },
                        { data: 'nominal_ppn', name: 'nominal_ppn', render: $.fn.dataTable.render.number('.', ',', 0) },
                        { data: 'total', name: 'total', render: $.fn.dataTable.render.number('.', ',', 0) },
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
            });
        </script>
    </x-slot:script>
</x-Layout.layout>
