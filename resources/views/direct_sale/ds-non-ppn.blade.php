<x-Layout.layout>
    <style>
        .center-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .center-container .card {
            max-width: 90%;
            margin: auto;
        }

        @media print {
            #print .header {
                margin-top: 10px;
            }

            #print,
            #print * {
                visibility: visible !important;
                font-family: 'Open Sans', sans-serif;
                font-size: .7rem !important;
                color: black !important;
            }

            #print {
                width: 100%;
                font-family: 'Open Sans', sans-serif;
            }

            .card {
                display: none !important;
            }

            #reset {
                all: unset !important;
            }
        }

        #table-barang td {
            padding: 0px;
        }

        .server-time {
            font-size: 18px;
            margin-bottom: 15px;
            margin-left: 150px;
            font-weight: bold;
            color: #fff04d;
            background-color: #ff0000;
            padding: 10px 15px;
            border-radius: 5px;
            display: inline-block;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
    <form action="{{ route('ds.ppn-store') }}" method="post" id="reset"
        onsubmit="return validateForm()">
        @csrf
        <div class="center-container" id="print">
            <div class="card bg-base-100 shadow-xl mb-5">
                <div class="card-body">
                    <h1 class="card-title mb-2">Form Direct Sale NON PPN</h1>
                    <div class="block overflow-x-auto w-full">
                        <table class="table w-full border-collapse" id="table-barang" style="font-size: .7rem">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th style="width: 230px">Barang</th>
                                    {{-- <th>Harsat Beli</th> --}}
                                    <th>Jumlah Jual</th>
                                    {{-- <th>Harsat Jual</th> --}}
                                    <th>Harga Jual</th>
                                    {{-- <th>Profit</th> --}}
                                </tr>
                            </thead>
                            <tbody id="tbody-list-barang">
                                @php
                                    $q = 5;
                                @endphp
                                <input type="hidden" name="q" id="q" value="{{ $q }}">
                                @for ($i = 1; $i <= $q; $i++)
                                    <input type="hidden" name="nama_satuan[]" id="nama_satuan-{{ $i }}" />
                                    <tr>
                                        <td class="text-center">{{ $i }}</td>
                                        <td>
                                             <select name="barang[]" id="barang-{{ $i }}"
                                                class="select2 form-control my-0" style="width: 500px; border:none">
                                                <option value=""></option>
                                                @foreach ($harga as $item)
                                                    <option value="{{ $item->id }}">{{ $item->nama }} ||
                                                        {{ $item->nama_satuan }} || Stock : {{ $item->sisa }} || {{ $item->no_bm }} ||
                                                        {{ number_format($item->harga_barang) }}</option>
                                                @endforeach
                                            </select>
                                        <td>
                                            <input type="number" step="any" style="width:120px"
                                                onchange="inputBarang()" name="jumlah_jual[]"
                                                id="jumlah_jual-{{ $i }}" class="form-control">
                                        </td>
                                        <td>
                                           <input type="text" readonly step="any" style="width:120px" name="harga[]" id="harga-{{ $i }}" class="form-control">
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <button id="btn_tambah" type="button" class="btn bg-blue-500 text-white">Tambah Baris</button>
                    <input type="hidden" name="total" id="total">
                    <button id="submit" type="submit" onclick="return confirm('Apakah anda yakin?')"
                        class="btn btn-sm w-full bg-green-500 text-white rounded-lg mt-3">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </form>


    <script>
$('.select2').selectize({
    onChange: function(value) {
        if (!value) return;

        const selectId = this.$input.attr('id');     // misal: barang-0
        const index = selectId.split('-')[1];        // ambil angka
        const hargaInput = document.getElementById('harga-' + index);
        console.log(value);

        // Kirim AJAX ke API
        $.ajax({
            url: '/get-harga',  // Ganti sesuai endpoint kamu
            method: 'GET',
            data: { id: value },
            success: function(response) {
                // Asumsikan response = { harga: 12345 }
                hargaInput.value = response.harga ?? '';
            },
            error: function() {
                hargaInput.value = '';
                console.error('Gagal ambil data harga dari API.');
            }
        });
    }
});
        $('#kepada').on('input', function() {
            var inputValue = $(this).val();
            var id = $("#ekspedisi_list option[value='" + inputValue + "']").data('id');
            var alamat = $("#ekspedisi_list option[value='" + inputValue + "']").data('alamat');
            var kota = $('#ekspedisi_list option[value="' + inputValue + '"]').data('kota');
            $('#txt_kepada').text(inputValue);
            $('#alamat_ekspedisi_txt').text(alamat);
            $('#kota_ekspedisi_txt').text(kota);
            $('#alamat_ekspedisi').val(alamat);
            $('#id_ekspedisi').val(id);
        });

        // $('#kepada').on('input', function () {
        //     var inputValue = $(this).val();
        //     $('#txt_kepada').text(inputValue);
        // });

        $('#no_po').on('input', function() {
            var inputValue = $(this).val();
            $('#txt_no_po').text(inputValue);
        });

        $('#jenis_barang').on('input', function() {
            var inputValue = $(this).val();
            $('#txt_jenis_barang').text(inputValue);
            var text = $("#jenis_barang_list option[value='" + inputValue + "']").data('text');
            $('#txt_jenis_barang').text(inputValue);
            $('#txt_total').text($('#jumlah').val() * $('#jumlah_satuan').val() * text);
            $('#total').val($('#jumlah').val() * $('#jumlah_satuan').val() * text);

        });

        $('#nama_kapal').on('input', function() {
            var inputValue = $(this).val();
            $('#txt_nama_kapal').text(inputValue);
        });

        $('#no_cont').on('input', function() {
            var inputValue = $(this).val();
            $('#txt_no_cont').text(inputValue);
        });

        $('#no_seal').on('input', function() {
            var inputValue = $(this).val();
            $('#txt_no_seal').text(inputValue);
        });

        $('#no_pol').on('input', function() {
            var inputValue = $(this).val();
            var id = $("#no_pol_list option[value='" + inputValue + "']").data('id');
            $('#id_nopol').val(id);
            $('#txt_no_pol').text(inputValue);
        });

        $('#no_job').on('input', function() {
            var inputValue = $(this).val();
            $('#txt_no_job').text(inputValue);
        });

        $('#tujuan').on('input', function() {
            var inputValue = $(this).val();
            var id = $("#customer_list option[value='" + inputValue + "']").data('id');
            $('#id_customer').val(id);
            $('#txt_tujuan').text(inputValue);
        });

        //$('#harga_jual').on('click', function () {
        //    var harga_jual = $('#harga_jual').val();
        //    var harga_beli = $('#harga_beli').val();
        //    var total = $('#txt_total').text();
        //    $('#profit').val((harga_jual * total) - (harga_beli * total));
        //});

        //jquery ready function
        $(document).ready(function() {
            $('#txt_nomor_surat').text($('#nomor_surat').val());
            $('#txt_kota_pengirim').text($('#kota_pengirim').val());
            $('#txt_nama_pengirim').text($('#nama_pengirim').val());
            $('#txt_nama_penerima').text($('#nama_penerima').val());
            $('#txt_no_po').text($('#no_po').val());
        });

        $("#kota_pengirim").on({
            input: function() {
                var inputValue = $(this).val();
                $('#txt_kota_pengirim').text(inputValue);
            },
            click: function() {
                var inputValue = $(this).val();
                $('#txt_kota_pengirim').text(inputValue);
            }
        });

        $("#tgl_sj").on({
            input: function() {
                var inputValue = $(this).val();
                let newData = new Date(inputValue).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit'
                });
                $('#txt_tgl_sj').text(newData);
            },
            click: function() {
                var inputValue = $(this).val();
                let newData = new Date(inputValue).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit'
                });
                $('#txt_tgl_sj').text(newData);
            }
        });

        $("#nama_pengirim").on({
            input: function() {
                var inputValue = $(this).val();
                $('#txt_nama_pengirim').text(inputValue);
            },
            click: function() {
                var inputValue = $(this).val();
                $('#txt_nama_pengirim').text(inputValue);
            }
        });

        $("#nama_penerima").on({
            input: function() {
                var inputValue = $(this).val();
                $('#txt_nama_penerima').text(inputValue);
            },
            click: function() {
                var inputValue = $(this).val();
                $('#txt_nama_penerima').text(inputValue);
            }
        });


       // Fungsi reusable: cukup dideklarasikan sekali
function initSelectizeBarang(selector) {
    $(selector).selectize({
        onChange: function(value) {
            if (!value) return;

            const selectId = this.$input.attr('id');
            const index = selectId.split('-')[1];
            const hargaInput = document.getElementById('harga-' + index);

            $.ajax({
                url: '/get-harga',
                method: 'GET',
                data: { id: value },
                success: function(response) {
                    hargaInput.value = response.harga ?? '';
                },
                error: function() {
                    hargaInput.value = '';
                    console.error('Gagal ambil data harga dari API.');
                }
            });
        }
    });
}

let q = 5; // inisialisasi variabel q

$('#btn_tambah').click(function() {
    q++;
    var html = `
        <tr>
            <td class="text-center">${q}</td>
            <td>
                <select name="barang[]" id="barang-${q}" class="select2 form-control my-0" style="width: 500px; border:none">
                    <option selected value=""></option>
                    @foreach ($harga as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }} ||
                            {{ $item->nama_satuan }} || Stock : {{ $item->sisa }} ||
                            {{ number_format($item->harga_barang) }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" style="width:120px" onchange="inputBarang()" name="jumlah_jual[]" id="jumlah_jual-${q}" class="form-control">
            </td>
            <td>
                <input type="text" style="width:120px" onchange="inputBarang()" name="harga[]" id="harga-${q}" class="form-control">
            </td>
        </tr>`;
    
    $('#tbody-list-barang').append(html);
    initSelectizeBarang('#barang-' + q);
});

    </script>

</x-Layout.layout>
