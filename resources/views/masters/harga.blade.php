<x-Layout.layout>
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
            background-color: #089d55;
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

        .select2-container--default .select2-selection--single {
            height: 40px !important;
            line-height: 40px !important;
            padding: 0 12px !important;
            border-radius: 0.375rem !important;
        }

        /* Untuk teks di dalam select */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
        }

        /* Untuk panah kanan */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
    </style>

    <!-- Link CSS untuk jqGrid dan jQuery UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <!-- jQuery dulu -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- jQuery UI (jika diperlukan jqGrid versi lama) -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- CSS jqGrid -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/free-jqgrid@4.15.5/css/ui.jqgrid.min.css">

    <!-- JS jqGrid -->
    <script src="https://cdn.jsdelivr.net/npm/free-jqgrid@4.15.5/js/jquery.jqgrid.min.js"></script>


    <!-- Card untuk tampilan laporan piutang -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <div id="dialog"></div>
    @if ($errors->any())
        <div id="error-alert"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 col-span-3"
            role="alert">
            <strong class="font-bold">Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" onclick="document.getElementById('error-alert').remove();"
                class="absolute top-0 right-0 px-4 py-3 text-2xl font-bold text-red-700 leading-none focus:outline-none">
                &times;
            </button>
        </div>
    @endif


    <x-master.card-master>
        <x-slot:tittle>Buat Harga</x-slot:tittle>

        <form action="{{ route('master.harga.tambah') }}" method="POST" class="grid grid-cols-3 gap-4 mt-3">
            @csrf

            {{-- Harga --}}
            <div class="col-span-1">
                <label class="label">
                    <span class="label-text">Harga <span class="text-red-500">*</span></span>
                </label>
                <input type="text" name="harga" placeholder="Masukkan harga"
                    class="input input-bordered w-full rounded-md" required oninput="formatRibuan(this)">
            </div>

            {{-- Tanggal Harga --}}
            <div class="col-span-1">
                <label class="label">
                    <span class="label-text">Tanggal Harga <span class="text-red-500">*</span></span>
                </label>
                <input type="date" name="tgl" class="input input-bordered w-full rounded-md" required>
            </div>

            {{-- Nama Barang --}}
            <div class="col-span-1">
                <label class="label">
                    <span class="label-text">Nama Barang <span class="text-red-500">*</span></span>
                </label>
                <select id="id_barang" name="id_barang" class="select select-bordered w-full rounded-md" required>
                    <option disabled selected>Pilih Barang</option>
                    @foreach ($barang as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }} || {{ $item->satuan->nama_satuan }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol --}}
            <div class="col-span-3 mb-4 text-center">
                <button type="submit" class="submit-button mt-8">Simpan</button>
            </div>
        </form>
    </x-master.card-master>



    <x-master.card-master>
        <x-slot:tittle>List Harga Belum Aktif</x-slot:tittle>
        <table id="hargaNonAktif"></table>
        <div id="hargaNonAktifPager"></div>
    </x-master.card-master>


    <x-master.card-master>
        <x-slot:tittle>List Harga Sudah Aktif</x-slot:tittle>
         <table id="hargaAktif"></table>
        <div id="hargaAktifPager"></div>
    </x-master.card-master>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <x-slot:script>
        <script>
            $(document).ready(function() {
                $('#id_barang').select2({});
            });

            function formatRibuan(input) {
                let value = input.value.replace(/\D/g, ''); // Menghapus semua karakter selain angka
                value = Number(value); // Mengonversi nilai menjadi angka

                // Menambahkan pemisah ribuan
                if (!isNaN(value)) {
                    input.value = value.toLocaleString(); // Format angka dengan pemisah ribuan
                }
            }

            function resizeGrid() {
    $("#hargaNonAktif").setGridWidth($("#hargaNonAktif").closest(".grid-container").width());
}
            $("#hargaNonAktif").jqGrid({
        url: '{{ route('harga.nonaktif.data') }}',
        datatype: "json",
        mtype: "GET",
        postData: {
            non_aktif : true
        },
        colModel: [
            {
               
                label: 'Tanggal',
                name: 'tgl',
                align: 'center',
                width: 100,
                formatter: 'date',
                formatoptions: { newformat: 'd/m/Y' }
            },
            { label: 'Barang', name: 'barang', width: 150,  },
            // { label: 'Status PPN', name: 'status_ppn', width: 120,  },
            {
                
                label: 'Nominal Harga',
                name: 'harga',
                width: 120,
                align: 'right'
            },
           {
            
                label: 'Aksi',
                name: 'aksi',
                width: 100,
                align: 'center',
                sortable: false,
                formatter: function(cellValue, options, rowObject) {
                    if (rowObject.aktif === 0) {
                        return `<button class="bg-red-500 hover:bg-yellow-300 text-white font-semibold py-1 px-2 rounded btn-delete" data-id="${rowObject.id}">Edit</button>`;
                    } else {
                        return ''; // atau return '—';
                    }
                }
            }
        ],
        jsonReader: {
            root: "rows",
            page: "page",
            total: "total",
            records: "records",
            repeatitems: false
        },
        pager: "#hargaNonAktifPager",
        rowNum: 10,
        rowList: [10, 20, 50],
        height: '100%',
        autowidth: true,
        shrinkToFit: true,
        viewrecords: true,
        caption: "Data Harga Belum Aktif",
        loadComplete: function () {
            resizeGrid();
        }
    });

    $(window).on('resize', function () {
        resizeGrid();
    });
        </script>
    </x-slot:script>
</x-Layout.layout>
