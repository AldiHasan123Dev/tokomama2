<x-Layout.layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            z-index: 1500;
            overflow-y: auto;
        }



        /* Mengatur gaya untuk input text */
        .form-control {
            width: 100%;
            /* Memastikan input memenuhi lebar wadah */
            padding: 10px;
            /* Memberikan padding dalam input */
            border: 1px solid #ccc;
            /* Warna border abu-abu */
            border-radius: 5px;
            /* Membulatkan sudut input */
            font-size: 14px;
            /* Ukuran font yang digunakan dalam input */
            transition: border-color 0.3s;
            /* Animasi transisi untuk warna border */
        }

        /* Mengubah gaya input saat mendapatkan fokus */
        .form-control:focus {
            border-color: #007bff;
            /* Mengubah warna border saat fokus */
            outline: none;
            /* Menghilangkan outline default */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            /* Memberikan efek bayangan */
        }

        /* Mengatur gaya untuk datalist */
        datalist {
            margin-top: 5px;
            /* Memberikan jarak di atas datalist */
        }

        /* Mengatur gaya untuk option dalam datalist (tidak bisa distyling di browser, tetapi bisa dicontohkan) */
        datalist option {
            padding: 5px;
            /* Padding untuk opsi */
            cursor: pointer;
            /* Mengubah kursor saat hover */
        }

        .select2-container {
            z-index: 10000 !important;
            /* Atur sesuai dengan z-index modal Anda */
        }

        .modal .select2-dropdown {
            position: absolute !important;
            top: 100% !important;
            /* Pastikan dropdown muncul di bawah */
            left: 0 !important;
            z-index: 1000 !important;
        }


        .modal-box {
            background-color: white;
            padding: 20px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-family: Arial, sans-serif;
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
            background-color: #28a745;
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

        .select2-container--open .select2-dropdown--below {
            display: block !important;
        }
    </style>
    <input type="hidden" id="nj" value="{{ $data[0]->nomor }}">
    <input type="hidden" id="tgl" value="{{ $tgl }}">

    <div id="dialog"></div>

    <x-jurnal.card-jurnal>
        <x-slot:tittle>Parameter</x-slot:tittle>
        <div class="grid grid-cols-3 gap-4  ">
            <p class="bg-gray-500 text-white p-1 text-center">[1] Customer</p>
            <p class="bg-gray-500 text-white p-1 text-center">[2] Supplier</p>
            <p class="bg-gray-500 text-white p-1 text-center">[3] Barang</p>
            <p class="bg-gray-500 text-white p-1 text-center">[4] Kuantitas</p>
            <p class="bg-gray-500 text-white p-1 text-center">[5] Satuan</p>
            <p class="bg-gray-500 text-white p-1 text-center">[6] Harsat Beli</p>
            <p class="bg-gray-500 text-white p-1 text-center">[7] Harsat Jual</p>
            <p class="bg-gray-500 text-white p-1 text-center">[8] Keterangan</p>
        </div>
    </x-jurnal.card-jurnal>
    <x-jurnal.card-jurnal>
        <x-slot:tittle>Edit Jurnal</x-slot:tittle>
        <div class="overflow-x-auto">
            <form action="{{ route('jurnal.edit.tglupdate') }}" method="post">
                @csrf
                <input name="tgl_input" type="date" style="width: 100%" class="mb-8 rounded-md" id="tgl_input">
                <input readonly name="nomor_jurnal_input" type="text" style="width: 100%"
                    class="mb-8 rounded-md bg-gray-100" id="nomor_jurnal">
                <button type="submit" style="margin-bottom: 20px;"
                    class="btn bg-green-500 font-semibold text-white">Simpan Tanggal</button>
            </form>

            <table id="table-editj" class="cell-border hover display nowrap">
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>ID</th>
                        <th>Nomor Jurnal</th>
                        <th>Akun</th>
                        <th>Nama Akun</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                        <th>Keterangan</th>
                        <th>Invoice</th>
                        <th>Invoice External</th>
                        <th>Nopol</th>
                        <th>Tipe</th>
                        <th>Keterangan Buku Besar Pembantu</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td><button class="text-yellow-400"
                                    onclick="editJurnal( '{{ $item->id }}', '{{ $item->nomor }}', '{{ $item->tgl }}', '{{ $item->debit }}', '{{ $item->kredit }}', '{{ $item->keterangan }}', '{{ $item->invoice }}', '{{ $item->invoice_external }}', '{{ $item->nopol }}', '{{ $item->tipe }}', '{{ $item->coa_id }}', '{{ $item->nama_akun }}', '{{ $item->no_akun }}', '{{ $item->keterangan_buku_besar_pembantu }}')"><i
                                        class="fa-solid fa-pencil"></i></button> |
                                <button id="delete-faktur-all" onclick="deleteItemJurnal('{{ $item->id }}')"
                                    class="text-red-600 font-semibold mb-3 self-end"><i
                                        class="fa-solid fa-trash"></i></button>
                            </td>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->nomor ?? '-' }}</td>
                            <td>{{ $item->no_akun ?? '-' }}</td>
                            <td>{{ $item->nama_akun ?? '-' }}</td>
                            <td class="text-end">{{ number_format($item->debit, 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item->kredit, 2, ',', '.') }}</td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                            <td>{{ $item->invoice == 0 ? '' : $item->invoice ?? '-' }}</td>
                            <td>{{ $item->invoice_external == 0 ? '' : $item->invoice_external ?? '-' }}</td>
                            <td>{{ $item->nopol ?? '-' }}</td>
                            <td>{{ $item->tipe ?? '-' }}</td>
                            <td>{{ $item->keterangan_buku_besar_pembantu ?? '-' }}</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @php
            $total_kredit = $data->sum('kredit');
            $total_debet = $data->sum('debit');
        @endphp
        <p class="font-bold text-2xl">Total Debet: {{ number_format($total_debet, 2, ',', '.') }} </p>
        <p class="font-bold text-2xl">Total Kredit: {{ number_format($total_kredit, 2, ',', '.') }}</span> </p>
    </x-jurnal.card-jurnal>

    <x-slot:script>
        <script src="https://cdn.datatables.net/2.1.0/js/dataTables.tailwindcss.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            let nomorJurnal = $("#nj").val();
            let tgl = $("#tgl").val();

            $("#tgl_input").val(tgl);
            $("#nomor_jurnal").val(nomorJurnal);

            let table = $(`#table-editj`).DataTable({
            pageLength: 25 // Menentukan jumlah baris per halaman
        });

            function editJurnal(id, nomor, tgl, debit, kredit, keterangan, invoice, invoice_external, nopol, tipe, coa_id,
                nama_akun, no_akun, keterangan_buku_besar_pembantu) {
                invoice = (invoice === '0') ? '' : invoice;
                invoice_external = (invoice_external === '0') ? '' : invoice_external;
                $("#dialog").html(`
                   <dialog id="my_modal_3" class="modal ">
    <div class="modal-box">
        <button class="close-button" onclick="document.getElementById('my_modal_3').close()">✕</button>
        <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 15px;">Edit Nomor Jurnal ${nomor} ID: (${id})</h3>
        <form action="{{ route('jurnal.edit.update') }}" method="post">
            @csrf

             <label class="form-label">Coa</label>
            <select class="select-field" name="coa_id" id="coa_id">
                <option value="${coa_id}" selected>${no_akun} ${nama_akun}</option>
                @foreach ($coa as $c)
                <option value="{{ $c->id }}">{{ $c->no_akun }} {{ $c->nama_akun }}</option>
                @endforeach
            </select>

              <label class="form-label">Invoice External</label>
            <select class="select-field" name="invoice_external" id="invext">
                <option value="${invoice_external}" selected>${invoice_external}</option>
                 <option value=""></option>
                @foreach ($invExtProc as $in)
                <option value="{{ $in }}">{{ $in }}</option>
                @endforeach
            </select>
            <label class="form-label">Invoice</label>
            <select class="select-field form-select " name="invoice" id="invoices">
                <option value="${invoice}" selected>${invoice}</option>
                <option value=""></option>
                @foreach ($invProc as $i)
                <option value="{{ $i }}">{{ $i }}</option>
                @endforeach
            </select>

              <label class="form-label h-auto">Nopol</label>
            <select class="form-select select-field" name="nopol" id="nopol">
                <option value="${nopol}" selected>${nopol}</option>
                <option value=""></option>
                @foreach ($nopol as $n)
                <option value="{{ $n->nopol }}">{{ $n->nopol }} </option>
                @endforeach
            </select>

             <label class="form-label h-auto">Keterangan Buku Pembantu</label>
            <select class="form-select select-field" name="keterangan_buku_besar_pembantu" id="keterangan_buku_besar_pembantu">
                <option value="${keterangan_buku_besar_pembantu}" selected>${keterangan_buku_besar_pembantu}</option>
                <option value=""></option>
                @foreach ($jurnals as $j)
                <option value="{{ $j->keterangan_buku_besar_pembantu }}">{{ $j->keterangan_buku_besar_pembantu }} </option>
                @endforeach
            </select>

            <label class="form-label">Debit</label>
            <input name="debit" type="text" class="input-field" value="${debit}" />

            <label class="form-label">Kredit</label>
            <input name="kredit" type="text" class="input-field" value="${kredit}" />

            <label class="form-label">Keterangan</label>
            <input name="keterangan" type="text" class="input-field" value="${keterangan}" />
            <span class="label-info">*ex: [1]customer [2]supplier [3]barang</span>

               <input name="id" type="hidden" value="${id}" />
               <input name="nomor" type="hidden" value="${nomor}" />
               
               <input name="tgl" type="hidden" value="${tgl}" />
               <input name="tipe" type="hidden" value="${tipe}" />

            <button type="submit" class="submit-button">Edit</button>
        </form>
    </div>
</dialog>
                `);


                my_modal_3.showModal();
                $('#invext').select2({
                    dropdownParent: $('#my_modal_3'),
                    dropdownAutoWidth: true,
                    width: '100%',
                    appendTo: 'body'
                });
                $('#coa_id').select2({
                    dropdownParent: $('#my_modal_3'),
                    dropdownAutoWidth: true,
                    width: '100%',
                    appendTo: 'body'
                });
                
                $('#invoices').select2({
                    dropdownParent: $('#my_modal_3'),
                    dropdownAutoWidth: true,
                    width: '100%',
                    appendTo: 'body'
                });
                $('#keterangan_buku_besar_pembantu').select2({
                    dropdownParent: $('#my_modal_3'),
                    dropdownAutoWidth: true,
                    width: '100%',
                    appendTo: 'body'
                });
                $('#nopol').select2({
                    dropdownParent: $('#my_modal_3'),
                    dropdownAutoWidth: true,
                    width: '100%',
                    appendTo: 'body'
                });
            }

            $.fn.select2.amd.define('select2/dropdown/position', [], function() {
                function PositionDropdown() {}
                PositionDropdown.prototype.bind = function(container, $container) {
                    container.on('results:all', function() {
                        let $dropdown = $container.data('select2').dropdown.$dropdown;
                        let offset = $container.offset();
                        $dropdown.css({
                            top: offset.top + $container.outerHeight(),
                            left: offset.left
                        });
                    });
                };
                return PositionDropdown;
            });

            function deleteItemJurnal(id) {
                if (confirm('Apakah anda ingin menghapus data ini?')) {
                    $.ajax({
                        method: 'post',
                        url: "{{ route('jurnal.item.delete') }}",
                        data: {
                            id: id
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            alert("Data Jurnal berhasil dihapus!");
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.log('Error:', error);
                            console.log('Status:', status);
                            console.dir(xhr);
                        }
                    });
                }
            }
        </script>
    </x-slot:script>
</x-Layout.layout>
