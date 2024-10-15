<x-Layout.layout>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.2/css/dataTables.dateTime.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.0/css/buttons.dataTables.css">
    <style>
        .red-row {
            background-color: yellow !important;
            color: black;
        }
    </style>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Laporan Omzet</x-slot:tittle>
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
                        <th rowspan="2">NO. </th>
                        <th rowspan="2">TGL STUFFING</th>
                        <th rowspan="2">NO. SURAT JALAN</th>
                        <th rowspan="2">NO. INV</th>
                        <th rowspan="2">TGL. INV</th>
                        <th rowspan="2">No. Faktur Pajak</th>
                        <th rowspan="2">NAMA KAPAL</th>
                        <th rowspan="2">Cont</th>
                        <th rowspan="2">Seal</th>
                        <th rowspan="2">Job</th>
                        <th rowspan="2">Nopol</th>
                        <th rowspan="2">JENIS BARANG</th>
                        <th rowspan="2">QUANTITY</th>
                        <th rowspan="2">SATUAN</th>
                        <th colspan="7" class="text-center border" style="border-left: 1px solid black; border-right: 1px solid black;">PENJUALAN (EXCL. PPN)</th>
                        <!-- <th>CUSTOMER</th>
                        <th>TUJUAN (Kota Cust)</th>
                        <th>HARGA JUAL</th>
                        <th>TOTAL TAGIHAN</th> -->
                        <th colspan="5" class="text-center border" style="border-left: 1px solid black; border-right: 1px solid black;">PEMBELIAN (EXCL. PPN)</th>
                        <!-- <th>HARGA BELI</th>
                        <th>TOTAL</th>
                        <th>TGL. PEMBAYARAN</th>
                        <th>NO. VOUCHER</th> -->
                        <th rowspan="2">MARGIN</th>
                        <th colspan="4" class="text-center border" style="border-left: 1px solid black; border-right: 1px solid black;">INCLUDE PPN</th>
                        <!-- <th>HARGA BELI (PPN)</th>
                        <th>MARGIN (PPN)</th> -->
                        <th colspan="3" class="text-center border" style="border-left: 1px solid black; border-right: 1px solid black;">HARSAT EXCL.PPN (SATUAN ORI)</th>
                        <!-- <th>BELI</th>
                        <th>JUAL</th> -->
                    </tr>
                    <tr>
                        <!-- <th>NO. </th>
                        <th>TGL STUFFING</th>
                        <th>NO. SURAT JALAN</th>
                        <th>NO. INV</th>
                        <th>No. Faktur Pajak</th>
                        <th>NAMA KAPAL</th>
                        <th>Cont</th>
                        <th>Seal</th>
                        <th>Job</th>
                        <th>Nopol</th>
                        <th>JENIS BARANG</th>
                        <th>QUANTITY</th>
                        <th>SATUAN</th> -->
                        <th style="border-left: 1px solid black;">PO CUSTOMER</th>
                        <th>CUSTOMER</th>
                        <th>TUJUAN (Kota Cust)</th>
                        <th>HARSAT JUAL</th>
                        <th>TOTAL</th>
                        <th>TGL. PEMBAYARAN</th>
                        <th style="border-right: 1px solid black;">NO.VOCHER</th>
                        <th>SUPPLIER</th>
                        <th>HARSAT BELI</th>
                        <th>TOTAL</th>
                        <th>TGL. PEMBAYARAN</th>
                        <th style="border-right: 1px solid black;">NO. VOUCHER</th>
                        <!-- <th>MARGIN</th> -->
                        <th style="border-left: 1px solid black;">PENJUALAN</th>
                        <th>PEMBELIAN</th>
                        <th style="border-right: 1px solid black;">SELISIH PPN</th>
                        <th style="border-right: 1px solid black;">MARGIN</th>
                        <th>SATUAN</th>
                        <th>BELI</th>
                        <th style="border-right: 1px solid black;">JUAL</th>
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
                pageLength: 100,
                ajax: {
                    url: "{{route('keuangan.omzet.datatable')}}",
                    dataSrc: "data"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'number'},
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
                    { data: 'satuan', name: 'Satuan Beli'},
                    { data: 'po_customer', name: 'no_po' },
                    { data: 'customer', name: 'Customer' },
                    { data: 'kota_cust', name: 'Kota Customer' },
                    { data: 'harga_jual', name: 'Harsat Jual', render: $.fn.dataTable.render.number('.') },
                    { data: 'total_tagihan', name: 'Total Tagihan', render: $.fn.dataTable.render.number('.') },
                    { data: 'tgl_penjualan', name: 'Tgl Penjualan'},
                    { data: 'no_vocherpenj', name: 'No Vocher Penjualan'},
                    { data: 'supplier', name: 'Supplier' },
                    { data: 'harga_beli', name: 'Harsat Beli', render: $.fn.dataTable.render.number('.') },
                    { data: 'total', name: 'hb x qty', render: $.fn.dataTable.render.number('.') },
                    { data: 'tgl_pembayaranpbl', name: 'Tanggal Bayar (jurnal)' },
                    { data: 'no_vocherpbl', name: 'Nomor Vocher (jurnal)' },
                    { data: 'margin', name: 'Margin', render: $.fn.dataTable.render.number('.') },
                    { data: 'harga_jual_ppn', name: 'Harga Beli (PPN)', render: $.fn.dataTable.render.number('.') },
                    { data: 'harga_beli_ppn', name: 'Harga Jual (PPN)', render: $.fn.dataTable.render.number('.') },
                    { data: 'margin_ppn', name: 'Margin (PPN)', render: $.fn.dataTable.render.number('.') },
                    { data: 'margin_cek', name: 'Margin CEK', render: $.fn.dataTable.render.number('.') },
                    { data: 'satuan_standar', name: 'Satuan Standar'},
                    { data: 'beli', name: 'Harsat Beli / value', render: $.fn.dataTable.render.number('.') },
                    { data: 'jual', name: 'Harsat Jual / value', render: $.fn.dataTable.render.number('.') },
                    { data: 'id_invoice', name: 'id', visible:false},
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