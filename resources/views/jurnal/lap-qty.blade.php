<x-layout.layout>   
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
        <x-slot:tittle>Lap QTY Jayapura</x-slot:tittle>

        <!-- Filter Form -->
        <div class="grid grid-cols-2 gap-2 justify-items-start mt-4 mb-4">
            <label class="form-control w-full max-w-xs mb-1">
                <div class="label">
                    <span class="label-text">Pilih Periode Bulan Barang Masuk</span>
                </div>
                <input type="month"
                    class="input input-sm input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                    id="periode" name="periode"
                    autocomplete="off" value="{{ date('Y-m') }}" />
            </label>
            <label class="form-control w-full max-w-xs mb-1">
                <div class="label">
                    <span class="label-text">Cek invoice external</span>
                </div>
                <input type="text"
                    class="input input-sm input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                    id="invx" name="invx" autocomplete="off" />
            </label>
        </div>

        <!-- jqGrid -->
        <table id="jqGrid1" class="table"></table>
        <div id="jqGridPager1"></div>
    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script>
            $(document).ready(function () {

                // Resize handler
                function resizeGrid() {
                    $("#jqGrid1").setGridWidth($('#jqGrid1').closest(".ui-jqgrid").parent().width(), true);
                }

                // Init jqGrid
                $("#jqGrid1").jqGrid({
                    url: '{{ route('data.qty') }}',
                    datatype: "json",
                    mtype: "GET",
                    postData: {
                        periode: function () {
                            return $('#periode').val();
                        },
                        invx: function () {
                            return $('#invx').val();
                        }
                    },
                    colModel: [
                        { label: 'Tanggal Terjurnal', name: 'tgl', align: 'center', width: 100 },
                        { label: 'No BM', name: 'no_bm', width: 100 },
                        { label: 'Inv Supplier', name: 'invoice_external', width: 150 },
                        { label: 'Barang', name: 'barang.nama', width: 150 },
                        { label: 'Supplier', name: 'supplier', width: 150 },
                        { label: 'Jumlah Beli', name: 'total_beli', align: 'right', width: 100 },
                        { label: 'Jumlah Jual', name: 'total_jual', align: 'right', width: 100 },
                        // { label: 'Sisa', name: 'sisa', align: 'right', width: 80 },
                        {
                            label: 'Harga Beli',
                            name: 'total_harga_beli',
                            align: 'right',
                            width: 120,
                            formatter: 'currency',
                            formatoptions: {
                                thousandsSeparator: ".",
                                decimalPlaces: 0
                            },
                            summaryType: 'sum'
                        }
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
                    pager: "#jqGridPager1",
                    rowNum: 30,
                    rowList: [30, 100, 200],
                    height: '100%',
                    userDataOnFooter: true,
                    autowidth: true,
                    shrinkToFit: true,
                    viewrecords: true,
                    footerrow: true,
                    userDataOnFooter: true,
                    caption: "Qty Jayapura",
                    loadComplete: resizeGrid
                });

                // Trigger reload saat filter berubah
                $('#periode, #invx').on('change', function () {
                    $("#jqGrid1").jqGrid('setGridParam', {
                        datatype: 'json',
                        page: 1
                    }).trigger('reloadGrid');
                });

                // Responsif saat resize window
                $(window).on('resize', resizeGrid);
            });
        </script>
    </x-slot:script>
</x-layout.layout>
