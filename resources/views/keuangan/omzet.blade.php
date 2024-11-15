<x-Layout.layout>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.2/css/dataTables.dateTime.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.0/css/buttons.dataTables.css">
    <style>
        /* Mengatur ukuran font, padding, dan jarak antar kolom */
        .text-right {
        text-align: right;
    }
        #table-omzet {
            font-size: 10px; /* Ukuran font lebih kecil */
            width: 100%;
            table-layout: auto;
            border-collapse: collapse; /* Menghilangkan jarak antar border kolom */
            border-spacing: 0; /* Mengatur jarak antar kolom menjadi 0 */
        }
    
        #table-omzet td, #table-omzet th {
            padding: 2px 3px; /* Padding minimal agar lebih rapat */
            font-size: 10px;
            text-align: center;
            border: 1px solid #ddd; /* Tambahkan garis tipis antar kolom */
        }
    
        /* Menyesuaikan lebar kolom secara khusus */
        #table-omzet th:nth-child(1), #table-omzet td:nth-child(1) {
            width: 40px;
        }
        #table-omzet th:nth-child(2), #table-omzet td:nth-child(2) {
            width: 80px;
        }
        #table-omzet th:nth-child(3), #table-omzet td:nth-child(3) {
            width: 10px;
        }
    </style>
    
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Laporan Trading</x-slot:tittle>
        <form action="{{route('keuangan.omzet.exportexcel')}}" method="post" class="self-end">
            @csrf
            <input type="hidden" name="start" id="startex" >
            <input type="hidden" name="end" id="endex" >
            <button type="submit" class="btn w-28 font-semibold text-white bg-green-500 hover:bg-green-400" id="excel">Export Excel</button>
        </form>
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
              
            <table class="cell-border hover display nowrap" id="table-omzet">
                <!-- head -->
                <thead>
                    <tr>
                        <th rowspan="2"  class="text-center border">NO. </th>
                        <th rowspan="2"  class="text-center border">TGL STUFFING</th>
                        <th rowspan="2"  class="text-center border">NO. SURAT JALAN</th>
                        <th rowspan="2"  class="text-center border">NO. INV</th>
                        <th rowspan="2"  class="text-center border">TGL. INV</th>
                        <th rowspan="2"  class="text-center border">No. Faktur Pajak</th>
                        <th rowspan="2"  class="text-center border">NAMA KAPAL</th>
                        <th rowspan="2"  class="text-center border">Cont</th>
                        <th rowspan="2"  class="text-center border">Seal</th>
                        <th rowspan="2"  class="text-center border">Job</th>
                        <th rowspan="2"  class="text-center border">Nopol</th>
                        <th rowspan="2"  class="text-center border">JENIS BARANG</th>
                        <th rowspan="2"  class="text-center border">QUANTITY</th>
                        <th rowspan="2"  class="text-center border">SATUAN</th>
                        <th colspan="5" class="text-center border">PENJUALAN (EXCL. PPN)</th>
                        <th colspan="5" class="text-center border">PEMBELIAN (EXCL. PPN)</th>
                        <th rowspan="2"  class="text-center border">MARGIN EXCLUDE</th>
                        <th colspan="4" class="text-center border">INCLUDE PPN</th>
                        <th colspan="3" class="text-center border">HARSAT EXCL.PPN (SATUAN ORI)</th>
                    </tr>
                    <tr>
                        <th  class="text-center border">PO CUSTOMER</th>
                        <th  class="text-center border">CUSTOMER</th>
                        <th  class="text-center border">TUJUAN (Kota Cust)</th>
                        <th  class="text-center border">HARSAT JUAL</th>
                        <th class="text-center border">TOTAL</th>
                        <th  class="text-center border">SUPPLIER</th>
                        <th  class="text-center border">HARSAT BELI</th>
                        <th  class="text-center border">TOTAL</th>
                        <th  class="text-center border">TGL. PEMBAYARAN</th>
                        <th  class="text-center border">NO. VOUCHER</th>
                        <th class="text-center border">PENJUALAN</th>
                        <th class="text-center border">PEMBELIAN</th>
                        <th class="text-center border">PORSI PPN</th>
                        <th class="text-center border">MARGIN</th>
                        <th class="text-center border">SATUAN</th>
                        <th class="text-center border">BELI</th>
                        <th class="text-center border">JUAL</th>
                    </tr>
                </thead>
                
                <tbody>
                </tbody>
            </table>
        </div>
    </x-keuangan.card-keuangan>

    {{-- <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script> --}}
    <x-slot:script>
        {{-- <script src="https://cdn.datatables.net/select/2.0.3/js/dataTables.select.js"></script>
        <script src="https://cdn.datatables.net/select/2.0.3/js/select.dataTables.js"></script> --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
        <script src="https://cdn.datatables.net/datetime/1.5.2/js/dataTables.dateTime.min.js"></script>
        <script src="https://cdn.datatables.net/2.1.0/js/dataTables.tailwindcss.js"></script>


        <script>

            let minDate, maxDate;
            DataTable.ext.search.push(function (settings, data, dataIndex) {
                let min = minDate.val();
                let max = maxDate.val();
                let date = new Date(data[4]);
            
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

            // Create date inputs
            minDate = new DateTime('#min', {
                format: 'YYYY-M-D'
            });

            maxDate = new DateTime('#max', {
                format: 'YYYY-M-D'
            });
            let table = $(`#table-omzet`).DataTable({
    pageLength: 20,
    ajax: {
        url: "{{route('keuangan.omzet.datatable')}}",
        dataSrc: "data"
    },
    columns: [
        { data: 'DT_RowIndex', name: 'number' },
        { data: 'tgl_stuffing', name: 'tgl_stufing' },
        { data: 'nomor_sj', name: 'No. Surat Jalan' },
        { data: 'invoice', name: 'No. Invoice' },
        { data: 'tgl_invoice', name: 'Tanggal Invoice' },
        { data: 'nomor_nsfp', name: 'nomor_nsfp' },
        { data: 'nama_kapal', name: 'Nama Kapal' },
        { data: 'cont', name: 'No. Cont' },
        { data: 'seal', name: 'No. Seal' },
        { data: 'job', name: 'No. Job' },
        { data: 'nopol', name: 'No. Polisi' },
        { data: 'nama_barang', name: 'Nama Barang' },
        { data: 'qty', name: 'Jumlah Beli', render: $.fn.dataTable.render.number('.') },
        { data: 'satuan', name: 'Satuan Beli' },
        { data: 'po_customer', name: 'no_po' },
        { data: 'customer', name: 'Customer' },
        { data: 'kota_cust', name: 'Kota Customer' },
        { data: 'harga_jual', align:'right', name: 'Harsat Jual', render: $.fn.dataTable.render.number('.'), className: 'text-right' },
        { data: 'total_tagihan', align:'right', name: 'Total Tagihan', render: $.fn.dataTable.render.number('.'), className: 'text-right' },
        { data: 'supplier', name: 'Supplier' },
        { data: 'harga_beli', align:'right', name: 'Harsat Beli', render: $.fn.dataTable.render.number('.'), className: 'text-right' },
        { data: 'total', align:'right', name: 'hb x qty', render: $.fn.dataTable.render.number('.'), className: 'text-right' },
        { data: 'tgl_pembayaranpbl', name: 'Tanggal Bayar (jurnal)' },
        { data: 'no_vocherpbl', name: 'Nomor Vocher (jurnal)' },
        { data: 'margin', align:'right', name: 'Margin', render: $.fn.dataTable.render.number('.'), className: 'text-right' },
        { data: 'harga_jual_ppn', align:'right', name: 'Harga Beli (PPN)', render: $.fn.dataTable.render.number('.'), className: 'text-right' },
        { data: 'harga_beli_ppn', align:'right', name: 'Harga Jual (PPN)', render: $.fn.dataTable.render.number('.'), className: 'text-right' },
        { data: 'margin_cek', align:'right', name: 'Margin CEK', render: $.fn.dataTable.render.number('.'), className: 'text-right' },
        { data: 'margin_ppn', align:'right', name: 'Margin (PPN)', render: $.fn.dataTable.render.number('.'), className: 'text-right' },
        { data: 'satuan_standar', name: 'Satuan Standar' },
        { data: 'beli', align:'right', name: 'Harsat Beli / value', render: $.fn.dataTable.render.number('.'), className: 'text-right' },
        { data: 'jual', align:'right', name: 'Harsat Jual / value', render: $.fn.dataTable.render.number('.'), className: 'text-right' },
        { data: 'id_invoice', name: 'id', visible: false }
    ],
    columnDefs: [
        {
            targets: [0, 1, 2],  // Indeks kolom yang ingin diatur lebar
            width: '40px'  // Menetapkan lebar kolom menjadi 150px
        },
        {
            targets: [3, 4, 5],  // Indeks kolom lain
            width: '120px'
        },
        {
            targets: '_all',  // Menetapkan lebar untuk kolom lainnya
            width: '100px'
        }
    ]
});


            table.on('draw', function() {
                table.rows().every(function() {
                    let data = this.data();
                    let margin = data.margin;

                    if(margin == 0) {
                        $(this.node()).addClass('red-row');
                    } else {
                        $(this.node()).removeClass('red-row');
                    }
                })
            })

            document.querySelectorAll('#min, #max').forEach((el) => {
              el.addEventListener('change', () => table.draw());
          });

          $("#min").on({
            change: function () {
              var inputValue = $(this).val();
              $('#startex').val(inputValue);
              console.log(inputValue); 
            }
          });

          $('#max').on({
            change: function () {
              var inputValue = $(this).val();
              $('#endex').val(inputValue);
            }
          });

            $('#form').submit(function (e) {
                e.preventDefault();
                var ids = $("#table-getfaktur input:checkbox:checked").map(function(){
                    return $(this).val();
                }).get();
                $('#id_transaksi').val(ids);
                this.submit();
            });
        </script>

    </x-slot:script>
</x-Layout.layout>