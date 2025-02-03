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
    </style>
    <form action="{{ route('surat-jalan.store') }}" target="_blank" method="post" id="reset" onsubmit="return validateForm()">
        @csrf
        <div class="center-container" id="print">
            <div class="card bg-base-100 shadow-xl mb-5">
                <div class="card-body">
                    <h1 class="card-title mb-2">Form Buat Surat Jalan</h1>
                    <div class="block overflow-x-auto w-full">
                        <table class="table w-full border-collapse" id="table-barang" style="font-size: .7rem">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th style="width: 230px">Barang</th>
                                    {{-- <th>Harsat Beli</th> --}}
                                    <th>Jumlah Jual</th>
                                    <th>Satuan Jual</th>
                                    {{-- <th>Harsat Jual</th> --}}
                                    <th>Keterangan</th>
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
                                                @foreach ($barang as $item)
                                                    <option value="{{ $item->id }}">{{ $item->nama }} ||
                                                        {{ $item->nama_satuan }} || {{ $item->sisa }} -
                                                        {{ $item->kode_objek }} || {{ $item->no_bm }}</option>
                                                @endforeach
                                            </select>
                                    </td>
                                    <td>
                                        <input type="number" style="width:120px" onchange="inputBarang()" name="jumlah_jual[]" id="jumlah_jual-{{ $i }}"
                                            class="form-control">
                                    </td>
                                    <td>
                                        <select name="satuan_jual[]" id="satuan_jual-{{ $i }}"
                                                onchange="inputBarang()" class=" m-1 form-select"
                                                >
                                                <option value=""></option>
                                                @foreach ($satuan as $item)
                                                    <option value="{{ $item->nama_satuan }}">{{ $item->nama_satuan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                    </td>
                                    <td>
                                        <input type="text" name="keterangan[]" id="keterangan-{{ $i }}" style="10000px">
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                        <datalist id="barang_id">
                            @foreach ($barang as $mb)
                            <option data-id="{{$mb->id}}" data-value="{{ $mb->value }}" data-satuan="{{ $mb->nama_satuan }}" data-nama="{{ $mb->nama_singkat }}" value="{{ $mb->id }}" >{{ $mb->id }} - {{ $mb->nama_singkat }} ({{ $mb->nama_satuan }})</option>
                            @endforeach
                        </datalist>
                        <datalist id="barang_list">
                            @foreach ($barang as $mb)
                            <option data-id="{{$mb->id}}" data-value="{{ $mb->value }}" data-satuan="{{ $mb->nama_satuan }}" value="{{ $mb->nama }}" >{{ $mb->nama_singkat }} ({{ $mb->nama_satuan }})</option>
                            @endforeach
                        </datalist>
                        <datalist id="satuan_beli_list">
                            @foreach ($satuan as $st)
                            <option data-id="{{$st->id}}" value="{{ $st->nama_satuan }}">{{ $st->nama_satuan }}</option>
                            @endforeach
                        </datalist>
                        <datalist id="supplier_list">
                            @foreach ($supplier as $sp)
                            <option data-id="{{$sp->id}}" data-nama="{{ $sp->nama }}" value="{{$sp->nama}}">{{$sp->nama}}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <button id="btn_tambah" type="button" class="btn bg-blue-500 text-white">Tambah Baris</button>
                    <input type="hidden" name="total" id="total">
                </div>
            </div>
        </div>

        <div class="center-container" id="print1">
            <div class="card bg-base-400 shadow-xl mb-5">
                <div class="card-body">
                    <div class="block overflow-x-auto w-full">
                        <div class="grid grid-cols-2 gap-4">
                            <div style="width: 500px;" class="center-container">
                                <input type="hidden" name="id_ekspedisi" id="id_ekspedisi">
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">Ekspedisi <span class="text-red-500">*</span></span>
                                    </div>
                                    <input type="text"
                                        class="input input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                                        id="kepada" name="kepada" list="ekspedisi_list" required autocomplete="off" oninput="this.value = this.value.toUpperCase();" />
                                    <datalist id="ekspedisi_list">
                                        @foreach ($ekspedisi as $item)
                                        <option data-id="{{$item->id}}" data-alamat="{{$item->alamat}}"
                                            data-kota="{{ $item->kota }}" value="{{ $item->nama }}">{{ $item->nama }}
                                        </option>
                                        @endforeach
                                    </datalist>
                                </label>
                            </div>
                            {{-- <div>
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">Alamat Ekspedisi <span class="text-red-500">*</span></span>
                                    </div>
                                    <input type="text"
                                        class="input input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                                        id="alamat_ekspedisi" name="alamat_ekspedisi" required />
                                </label>
                            </div> --}}
                            <input type="hidden" name="kota_ekspedisi" id="kota_ekspedisi">
                            <!-- <div>
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">Jumlah</span>
                                    </div>
                                    <input type="number"
                                        class="input input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                                        id="jumlah" min="0" name="jumlah" />
                                </label>
                            </div>
                            <div>
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">Satuan</span>
                                    </div>
                                    <input type="text"
                                        class="input input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                                        id="satuan" name="satuan" placeholder="(ZAK, BALL, KARTON, DLL)" />
                                </label>
                            </div> -->
                            <div style="width: 500px;" class="center-container">
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">No. Pol <span class="text-red-500">*</span></span>
                                    </div>
                                    <input type="text"
                                        class="input input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                                        id="no_pol" name="no_pol" list="no_pol_list" autocomplete="off" oninput="this.value = this.value.toUpperCase();" />
                                    <input type="hidden" name="id_nopol" id="id_nopol">
                                    <datalist id="no_pol_list">
                                        @foreach ($nopol as $np)
                                        <option data-id="{{ $np->id }}}" value="{{ $np->nopol }}">{{ $np->nopol }}</option>
                                        @endforeach
                                    </datalist>
                                </label>
                            </div>
                            <div style="width: 500px;" class="center-container">
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">No. PO <span class="text-red-500">*</span></span>
                                    </div>
                                    <input type="text"
                                        class="input input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                                        id="no_po" name="no_po" value="-"  oninput="this.value = this.value.toUpperCase();" required />
                                </label>
                            </div>
                            <div style="width: 500px;" class="center-container">
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">Tujuan/NamaCustomer <span class="text-red-500">*</span></span>
                                    </div>
                                    <input type="text"
                                        class="input input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                                        id="tujuan" name="tujuan" list="customer_list" required autocomplete="off"  oninput="this.value = this.value.toUpperCase();"/>
                                    <input type="hidden" name="id_customer" id="id_customer">
                                    <datalist id="customer_list">
                                        @foreach ($customer as $mb)
                                        <option data-id="{{$mb->id}}" value="{{ $mb->nama }}">{{ $mb->nama }}</option>
                                        @endforeach
                                    </datalist>
                                </label>
                            </div>
                            <div style="width: 500px;" class="center-container">
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">Kota Pengirim <span class="text-red-500">*</span></span>
                                    </div>
                                    <input type="text"
                                        class="input input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                                        id="kota_pengirim" name="kota_pengirim" value="Jayapura"  oninput="this.value = this.value.toUpperCase();" required />
                                </label>
                            </div>
                            <div style="width: 500px;" class="center-container">
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">Nama Pengirim <span class="text-red-500">*</span></span>
                                    </div>
                                    <input type="text"
                                        class="input input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                                        id="nama_pengirim" name="nama_pengirim" value="PIC 1"  oninput="this.value = this.value.toUpperCase();" required />
                                </label>
                            </div>
                            {{-- <div style="width: 500px;" class="center-container">
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">Nama Penerima <span class="text-red-500">*</span></span>
                                    </div>
                                    <input type="text"
                                        class="input input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                                        id="nama_penerima" name="nama_penerima" value="IFAN"  oninput="this.value = this.value.toUpperCase();" required />
                                </label>
                            </div> --}}
                            <div style="width: 500px;" class="center-container">
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">Tanggal Surat Jalan <span
                                                class="text-red-500">*</span></span>
                                    </div>
                                    <input type="date"
                                        class="input input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                                        id="tgl_sj" name="tgl_sj" value="{{ date('Y-m-d') }}" required />
                                </label>
                            </div>
                        </div>
                    </div>
                    <button id="submit" type="submit" onclick="return confirm('Apakah anda yakin?')"
                class="btn btn-sm w-full bg-green-500 text-white rounded-lg mt-3">
                Simpan
            </button>
                </div>
            </div>
        </div>
    </form>
    <script>
document.getElementById("submit").addEventListener("click", function (e) {
    let isFilled = true; // Menganggap form terisi sampai terbukti tidak terisi
    const rows = document.querySelectorAll("#tbody-list-barang tr");

    rows.forEach((row) => {
        const barang = row.querySelector(`[name="barang[]"]`).value.trim();
        const jumlahBeli = row.querySelector(`[name="jumlah_beli[]"]`).value.trim();
        const satuanBeli = row.querySelector(`[name="satuan_beli[]"]`).value.trim();
        const supplier = row.querySelector(`[name="supplier[]"]`).value.trim();

        // Cek apakah ada kolom yang kosong
        if (barang === "" || jumlahBeli === "" || satuanBeli === "" || supplier === "") {
            isFilled = false; // Set false jika ada input yang kosong
        }
    });

    if (!isFilled) {
        e.preventDefault(); // Menghentikan form submit
        alert("Masih ada input yang belum terisi, silahkan diisi terlebih dahulu");
    }
});



        function validateForm() {
        let valid = true;
        let fields = [
            {id: "kepada", name: "Ekspedisi"},
            {id: "nama_kapal", name: "Nama Kapal"},
            {id: "no_cont", name: "No. Cont"},
            {id: "no_seal", name: "No. Seal"},
            {id: "no_pol", name: "No. Pol"},
            {id: "no_po", name: "No. PO"},
            {id: "no_job", name: "No. Job"},
            {id: "tujuan", name: "Tujuan/Nama Customer"},
            {id: "kota_pengirim", name: "Kota Pengirim"},
            {id: "nama_pengirim", name: "Nama Pengirim"},
            {id: "nama_penerima", name: "Nama Penerima"},
            {id: "tgl_sj", name: "Tanggal Surat Jalan"}
        ];

        fields.forEach(function(field) {
            let input = document.getElementById(field.id);
            if (input && input.value.trim() === "") {
                alert(`Input ${field.name} tidak boleh kosong.`);
                input.focus();
                valid = false;
                return false;
            }
        });

        return valid;
    }
   
    
        $(function () {
            $(".select2").selectize();
        });
        $('#kepada').on('input', function () {
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

        $('#no_po').on('input', function () {
            var inputValue = $(this).val();
            $('#txt_no_po').text(inputValue);
        });

        $('#jenis_barang').on('input', function () {
            var inputValue = $(this).val();
            $('#txt_jenis_barang').text(inputValue);
            var text = $("#jenis_barang_list option[value='" + inputValue + "']").data('text');
            $('#txt_jenis_barang').text(inputValue);
            $('#txt_total').text($('#jumlah').val() * $('#jumlah_satuan').val() * text);
            $('#total').val($('#jumlah').val() * $('#jumlah_satuan').val() * text);

        });

        $('#nama_kapal').on('input', function () {
            var inputValue = $(this).val();
            $('#txt_nama_kapal').text(inputValue);
        });

        $('#no_cont').on('input', function () {
            var inputValue = $(this).val();
            $('#txt_no_cont').text(inputValue);
        });

        $('#no_seal').on('input', function () {
            var inputValue = $(this).val();
            $('#txt_no_seal').text(inputValue);
        });

        $('#no_pol').on('input', function () {
            var inputValue = $(this).val();
            var id = $("#no_pol_list option[value='" + inputValue + "']").data('id');
            $('#id_customer').val(id);
            $('#txt_no_pol').text(inputValue);
        });

        $('#no_job').on('input', function () {
            var inputValue = $(this).val();
            $('#txt_no_job').text(inputValue);
        });

        $('#tujuan').on('input', function () {
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
        $(document).ready(function () {
            $('#txt_nomor_surat').text($('#nomor_surat').val());
            $('#txt_kota_pengirim').text($('#kota_pengirim').val());
            $('#txt_nama_pengirim').text($('#nama_pengirim').val());
            $('#txt_nama_penerima').text($('#nama_penerima').val());
            $('#txt_no_po').text($('#no_po').val());
            $('#txt_no_pol').text($('#no_pol').val());
        });

        $("#kota_pengirim").on({
            input: function () {
                var inputValue = $(this).val();
                $('#txt_kota_pengirim').text(inputValue);
            },
            click: function () {
                var inputValue = $(this).val();
                $('#txt_kota_pengirim').text(inputValue);
            }
        });

        $("#tgl_sj").on({
            input: function () {
            var inputValue = $(this).val();
            let newData = new Date(inputValue).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: '2-digit'
            });
            $('#txt_tgl_sj').text(newData);
        },
            click: function () {
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
            input: function () {
                var inputValue = $(this).val();
                $('#txt_nama_pengirim').text(inputValue);
            },
            click: function () {
                var inputValue = $(this).val();
                $('#txt_nama_pengirim').text(inputValue);
            }
        });

        $("#nama_penerima").on({
            input: function () {
                var inputValue = $(this).val();
                $('#txt_nama_penerima').text(inputValue);
            },
            click: function () {
                var inputValue = $(this).val();
                $('#txt_nama_penerima').text(inputValue);
            }
        });

        let q = 5;

            $('#btn_tambah').click(function() {
                q++;
                var html = `
                                <tr>
                                    <input type="hidden" name="nama_satuan[]" id="nama_satuan-${q}" />
                                    <td class="text-center">${q}</td>
                                    <td>
                                        <select name="barang[]" id="barang-${q}" class="select2 form-control my-0" style="width: 300px; border:none">
                                            <option value=""></option>
                                            @foreach ($barang as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }} || {{ $item->nama_satuan }} || {{ $item->value }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" style="width:120px" onchange="inputBarang()" name="jumlah_beli[]" id="jumlah_beli-${q}"
                                            class="form-control">
                                    </td>
                                    <td>
                                            <select name="satuan_beli[]" id="satuan_beli-${q}"
                                                onchange="inputBarang()" class="m-1 form-select">
                                                <option value=""></option>
                                                @foreach ($satuan as $item)
                                                    <option value="{{ $item->nama_satuan }}">{{ $item->nama_satuan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                    </td>
                                    <td>
                                        <input type="number" style="width:120px" onchange="inputBarang()" name="jumlah_jual[]" id="jumlah_jual-${q}"
                                            class="form-control">
                                    </td>
                                    <td>
                                        <input type="text" style="width:120px" onchange="inputBarang()" name="satuan_jual[]" id="satuan_jual-${q}"
                                            class="form-control" placeholder="(ZAK, BALL, KARTON, DLL)" list="satuan_jual_list" autocomplete="off">
                                    </td>
                                    <td>
                                        <select name="supplier[]" id="supplier-{{ $i }}" class="select2 form-control my-0" style="width: 230px; border:none">
                                            <option value=""></option>
                                            @foreach ($supplier as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" style="width:120px" onchange="inputBarang()" name="keterangan[]" id="keterangan-${q}" class="form-control">
                                    </td>
                                </tr>`;
                $('#tbody-list-barang').append(html);
                $(".select2").selectize();
            });

        // function inputBarang() {
        //     for(let i = 1; i <= q; i++) {
        //         const jumlah_beli = $('#jumlah_beli-' + i).val();
        //         const satuan_beli = $('#satuan_beli-' + i).val();
        //         const jumlah_jual =  $('#jumlah_jual-' + i).val(jumlah_beli);
        //         const satuan_jual = $('#satuan_jual-' + i).val(satuan_beli);
                
        //     }
        //     let text = '';
        //     for (let i = 1; i <= q; i++) {
        //         const idbarang = $('#id_barangs-' + i).val();
        //         const barang = $('#barang-' + i).val();
        //         const jumlah_beli = $('#jumlah_beli-' + i).val();
        //         const satuan_beli = $('#satuan_beli-' + i).val();
        //         // const harga_beli = $('#harga_beli-' + i).val();
        //         const jumlah_jual =  $('#jumlah_jual-' + i).val();
        //         const satuan_jual = $('#satuan_jual-' + i).val();
        //         const keterangan = $('#keterangan-' + i).val();
        //         // const harga_jual = $('#harga_jual-' + i).val();
        //         const nama_barangs = $('#barang-' + i).find('option:selected').data('nama');
        //         const value_barangs = $('#barang-' + i).find('option:selected').data('value');
        //         const satuan_barangs = $('#barang-' + i).find('option:selected').data('satuan');
        //         if (barang != '' && typeof (barang) != undefined) {
        //             $("#satuan_beli-" + i).prop('required',true);
        //             $("#satuan_jual-" + i).prop('required',true);
        //             $("#supplier-" + i).prop('required',true);
                    

        //             var id_barang = $("#barang_list option[value='" + barang + "']").data('id');
        //             // console.log(id_barang)
        //             var value_barang = $("#barang_list option[value='" + barang + "']").data('value');
        //             var nama_satuan = $("#barang_list option[value='" + barang + "']").data('satuan');
        //             $("#id_barang-" + i).val(id_barang);
        //             if (satuan_jual.includes(nama_satuan)){
        //                 var total_jumlah = parseInt(jumlah_jual);
        //             } else {
        //                 var total_jumlah = parseFloat(value_barang) * parseInt(jumlah_jual);
        //             }
        //             var txt_total = '';
        //             //console.log("Satuan jual = " + satuan_jual);
        //             //console.log("Nama Satuan = " + nama_satuan);
        //             // if(barang.includes("@")){
        //                 if(satuan_jual.includes(nama_satuan)) {
        //                     txt_total += `<p>${keterangan!=''?' = '+keterangan:''}</p>`;
        //                 } else {
        //                     txt_total += `<p>(Total: ${total_jumlah} ${nama_satuan} ${keterangan!=''?' = '+keterangan:''})</p>`;
        //                 }
        //             // }
        //             text += `
        //                     <div class="flex justify-between mt-3">
        //                         <span>${barang}</span>
        //                         <span>(${jumlah_jual} ${satuan_jual})</span>
        //                     </div>
        //                     ${txt_total}
        //                     `;
        //                     $('#txt_nomor' + i).html(i);

        //                     // var test = $('#profit-' + i).val(jumlah_jual * harga_jual - jumlah_beli * harga_beli);
        //         } else {
        //             $("#satuan_beli-" + i).prop('required',false);
        //             $("#satuan_jual-" + i).prop('required',false);
        //             $("#supplier-" + i).prop('required',false);
        //         }

        //         $('#jumlah_beli-' + i).on('input', function () {
        //             var inputValue = $(this).val();
        //             $('#txt_jumlah' + i).html(inputValue);
        //         });

        //         $('#satuan_beli-' + i).on('input', function () {
        //             var inputValue = $(this).val();
        //             $('#txt_satuan' + i).html(inputValue);
        //         });

        //     }
        //     $('#barang-list').html(text);
        // }
    </script>

</x-Layout.layout>