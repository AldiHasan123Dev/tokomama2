<x-Layout.layout>
    <style>
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
        .date-input {
    margin-top: 10px;
    margin-bottom: 10px;
    width: 920px;
    padding: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}
.input-container {
    display: flex;
    flex-direction: column;
    gap: 5px;
    font-size: 14px;
}
.card-gray {
    background-color: #e3e6ea; /* Warna abu-abu terang */
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    width: 950px;
    margin-top: 10px;
    margin-bottom: 10px;
}

.input-container {
    display: flex;
    flex-direction: column;
    gap: 5px;
    font-size: 14px;
}
    </style>
    <form action="{{ route('barang_masuk.store') }}" method="post" id="reset" onsubmit="return validateForm()">
        @csrf
        <div class="center-container" id="print">
            <div class="card bg-base-100 shadow-xl mb-5">
                <div class="card-body">
                    <h1 class="card-title mb-2">Form Barang Masuk</h1>
                    <div class="block overflow-x-auto w-full">
                        <div class="card-gray">
                            <div class="input-container">
                                <label for="tgl_bm">Tanggal BM:</label>
                                <input type="date" name="tgl_bm" id="tgl_bm" class="date-input">
                            </div>
                        </div>
                        
                        
                        
                        <table class="table w-full border-collapse" id="table-barang" style="font-size: .7rem">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th style="width: 230px">Barang</th>
                                    {{-- <th>Harsat Beli</th> --}}
                                    <th>Jumlah Beli</th>
                                    <th>Satuan Beli</th>
                                    {{-- <th>Harsat Jual</th> --}}
                                    <th>Supplier</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-list-barang">
                                @php
                                $q = 5;
                                @endphp
                                <input type="hidden" name="q" id="q" value="{{ $q }}">
                                <input type="hidden" name="tgl" value="{{ date('Y-m-d') }}">
                                @for ($i = 1; $i <= $q; $i++)
                                <input type="hidden" name="nama_satuan[]" id="nama_satuan-{{ $i }}" />
                                <tr>
                                    <td class="text-center">{{ $i }}</td>
                                    <td>
                                       <select name="barang[]" id="barang-{{ $i }}"
                                                class="select2 form-control my-0" style="width: 300px; border:none">
                                                <option value=""></option>
                                                @foreach ($barang as $item)
                                                    <option value="{{ $item->id }}">{{ $item->nama }} ||
                                                        {{ $item->nama_satuan }} || {{ $item->value }} -
                                                        {{ $item->kode_objek }}</option>
                                                @endforeach
                                            </select>
                                    </td>
                                    <td>
                                        <input type="number" style="width:120px" onchange="inputBarang()" name="jumlah_beli[]" id="jumlah_beli-{{ $i }}"
                                            class="form-control">
                                    </td>
                                    <td>
                                        <select name="satuan_beli[]" id="satuan_beli-{{ $i }}"
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
                                        <select  name="supplier[]" id="supplier-{{ $i }}" class="select2 form-control my-0" style="width: 200px; border:none">
                                            <option value=""></option>
                                            @foreach ($supplier as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
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
    let isFilled = false; // Untuk mengecek apakah ada data yang diisi
    const rows = document.querySelectorAll("#tbody-list-barang tr");

    rows.forEach((row) => {
        const barang = row.querySelector(`[name="barang[]"]`).value.trim();
        const jumlahBeli = row.querySelector(`[name="jumlah_beli[]"]`).value.trim();
        const satuanBeli = row.querySelector(`[name="satuan_beli[]"]`).value.trim();
        const supplier = row.querySelector(`[name="supplier[]"]`).value.trim();

        // Cek apakah minimal salah satu kolom dalam baris terisi
        if (barang !== "" && jumlahBeli !== "" && satuanBeli !== "" && supplier !== "") {
            isFilled = true;
        }
    });

    if (!isFilled) {
        e.preventDefault(); // Menghentikan form submit
        alert("Masih ada input yang belum teriisi, silahkan diisi terlebih dahulu");
    }
});
        $(function () {
            $(".select2").selectize();
        });
        //jquery ready function
        let q = 5;

            $('#btn_tambah').click(function() {
                q++;
                var html = `
                                <tr>
                                    <input type="hidden" name="nama_satuan[]" id="nama_satuan-${q}" />
                                    <td class="text-center">${q}</td>
                                    <td>
                                        <select name="barang[]" id="barang-${q}" class="select2 form-control my-0" style="width: 500px; border:none">
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
                                        <select name="supplier[]" id="supplier-${q}" class="select2 form-control my-0" style="width: 300px; border:none">
                                            <option value=""></option>
                                            @foreach ($supplier as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" style="11000px" onchange="inputBarang()" name="keterangan[]" id="keterangan-${q}" class="form-control">
                                    </td>
                                    <td>
                                        <input type="datetime" style="width:120px" name="tgl_bm[]" id="tgl_bm-${q}"
                                            class="form-control">
                                    </td>
                                </tr>`;
                $('#tbody-list-barang').append(html);
                $("#barang-" + q).selectize();
                $("#supplier-" + q).selectize();
            });

        function inputBarang() {
            for(let i = 1; i <= q; i++) {
                const jumlah_beli = $('#jumlah_beli-' + i).val();
                const satuan_beli = $('#satuan_beli-' + i).val();
                const jumlah_jual =  $('#jumlah_jual-' + i).val(jumlah_beli);
                const satuan_jual = $('#satuan_jual-' + i).val(satuan_beli);
                
            }
            let text = '';
            for (let i = 1; i <= q; i++) {
                const idbarang = $('#id_barangs-' + i).val();
                const barang = $('#barang-' + i).val();
                const jumlah_beli = $('#jumlah_beli-' + i).val();
                const satuan_beli = $('#satuan_beli-' + i).val();
                // const harga_beli = $('#harga_beli-' + i).val();
                const jumlah_jual =  $('#jumlah_jual-' + i).val();
                const satuan_jual = $('#satuan_jual-' + i).val();
                const keterangan = $('#keterangan-' + i).val();
                // const harga_jual = $('#harga_jual-' + i).val();
                const nama_barangs = $('#barang-' + i).find('option:selected').data('nama');
                const value_barangs = $('#barang-' + i).find('option:selected').data('value');
                const satuan_barangs = $('#barang-' + i).find('option:selected').data('satuan');
                if (barang != '' && typeof (barang) != undefined) {
                    $("#satuan_beli-" + i).prop('required',true);
                    $("#satuan_jual-" + i).prop('required',true);
                    $("#supplier-" + i).prop('required',true);
                    

                    var id_barang = $("#barang_list option[value='" + barang + "']").data('id');
                    // console.log(id_barang)
                    var value_barang = $("#barang_list option[value='" + barang + "']").data('value');
                    var nama_satuan = $("#barang_list option[value='" + barang + "']").data('satuan');
                    $("#id_barang-" + i).val(id_barang);
                    if (satuan_jual.includes(nama_satuan)){
                        var total_jumlah = parseInt(jumlah_jual);
                    } else {
                        var total_jumlah = parseFloat(value_barang) * parseInt(jumlah_jual);
                    }
                    var txt_total = '';
                    //console.log("Satuan jual = " + satuan_jual);
                    //console.log("Nama Satuan = " + nama_satuan);
                    // if(barang.includes("@")){
                        if(satuan_jual.includes(nama_satuan)) {
                            txt_total += `<p>${keterangan!=''?' = '+keterangan:''}</p>`;
                        } else {
                            txt_total += `<p>(Total: ${total_jumlah} ${nama_satuan} ${keterangan!=''?' = '+keterangan:''})</p>`;
                        }
                    // }
                    text += `
                            <div class="flex justify-between mt-3">
                                <span>${barang}</span>
                                <span>(${jumlah_jual} ${satuan_jual})</span>
                            </div>
                            ${txt_total}
                            `;
                            $('#txt_nomor' + i).html(i);

                            // var test = $('#profit-' + i).val(jumlah_jual * harga_jual - jumlah_beli * harga_beli);
                } else {
                    $("#satuan_beli-" + i).prop('required',false);
                    $("#satuan_jual-" + i).prop('required',false);
                    $("#supplier-" + i).prop('required',false);
                }

                $('#jumlah_beli-' + i).on('input', function () {
                    var inputValue = $(this).val();
                    $('#txt_jumlah' + i).html(inputValue);
                });

                $('#satuan_beli-' + i).on('input', function () {
                    var inputValue = $(this).val();
                    $('#txt_satuan' + i).html(inputValue);
                });

            }
            $('#barang-list').html(text);
        }
    </script>

</x-Layout.layout>