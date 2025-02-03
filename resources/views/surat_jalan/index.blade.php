<x-Layout.layout>
    <style>
        .modal {
            top: 0;
            left: 0;
            width: 50%;
            padding: 10px;
            z-index: 1000;
        }

        .edit-button {
            display: inline-block;
            padding: 10px 10px;
            background-color: #eb7c06;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .edit-button:hover {
            background-color: #bc7812;
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
    </style>
    <div id="dialog"></div>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>List Surat Jalan</x-slot:tittle>
        <a href="{{ route('surat-jalan.editBarang') }}" class="edit-button mt-5">Edit by Barang</a>
        <div class="overflow-x-auto">
            <table class="table" id="table-getfaktur">
                <!-- head -->
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>Tanggal SJ</th>
                        <th>Invoice</th>
                        <th>No. Surat</th>
                        <th>No. BM</th>
                        <th>Kepada</th>
                        <th>No. Pol</th>
                        <th>No. Job</th>
                        <th>Profit</th>
                        <th>No PO</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </x-keuangan.card-keuangan>

    {{-- <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script> --}}
    <x-slot:script>
        <script>
            let table = $(`#table-getfaktur`).DataTable({
                pageLength: 100,
                ajax: {
                    method: "POST",
                    url: "{{ route('surat-jalan.data') }}",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                // scrollX:true,
                columns: [{
                        data: 'aksi',
                        name: 'aksi'
                    },
                    {
                        data: 'tgl_sj',
                        name: 'Tanggal SJ'
                    },
                    {
                        data: 'invoice',
                        name: 'No. Invoice'
                    },
                    {
                        data: 'nomor_surat',
                        name: 'No. Surat'
                    },
                    {
                        data: 'no_bm',
                        name: 'No. BM'
                    },
                    {
                        data: 'kepada',
                        name: 'kepada'
                    },
                    {
                        data: 'no_pol',
                        name: 'no_pol'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        visible: false
                    },
                    {
                        data: 'profit',
                        name: 'profit'
                    },
                    {
                        data: 'no_po',
                        name: 'no_po'
                    },

                ]
            });

            function getData(id, invoice, nomor_surat, kepada, jumlah, satuan, no_pol,
                tgl_sj, no_po) {

                $('#dialog').html(`<dialog id="my_modal_5" class="modal">
                <div class="modal-box w-11/12 max-w-2xl pl-10">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                    <h3 class="text-lg font-bold mt-3">Edit Data</h3>
                    <form action="{{ route('surat-jalan.data.edit') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="${id}" class="border-none" />
                    <input type="hidden" name="invoice" value="${invoice}" class="input-field"  />
                     <label class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_surat" value="${nomor_surat}" class="input-field" readonly />
                        <label class="form-label">Nomor Pol</label>
                         <select class="select-field" name="no_pol" id="nopol">
                        @foreach ($nopol as $n)
                            <option value="{{ $n->nopol }}" ${no_pol === '{{ $n->nopol }}' ? 'selected' : ''}>{{ $n->nopol }}</option>
                        @endforeach
                    </select>
                         <label class="form-label">Kepada</label>
                        <input type="text" name="kepada" value="${kepada}" class="input-field" />
                    <label class="form-label">Nomor PO </label>
                        <input type="text" name="no_po" value="${no_po}" class="input-field" />
                    <label class="form-label">Tanggal Surat Jalan</label>
                        <input type="date" name="tgl_sj" value="${tgl_sj}" class="input-field" />
                    <button type="submit" class="submit-button">Edit</button>
                    </form>
                </div>
                </dialog>`);
                my_modal_5.showModal();
                $('#nopol').select2({
                    dropdownParent: $(`#my_modal_5`),
                    width: '100%',
                    height: '40px' // Atur lebar select2 di sini
                });

                $('#nopol').on('select2:open', function() {
                    $('.select2-results__options').css({
                        'max-height': '200px', // Atur tinggi dropdown select2 di sini
                        'overflow-y': 'auto' // Mengaktifkan scroll jika tinggi melebihi batas
                    });
                });

            }

            function deleteData(id) {
                // Menampilkan ID yang akan dihapus
                console.log("ID yang akan dihapus:", id);

                if (confirm('Apakah anda yakin akan menghapus data ini?')) {
                    // Konfirmasi kedua
                    if (confirm(
                            'DATA YANG AKAN DIHAPUS INI, AKAN HILANG PERMANEN DAN TIDAK BISA DIKEMBALIKAN LAGI, APAKAH ANDA YAKIN?'
                            )) {
                        $.ajax({
                            method: 'POST',
                            url: "{{ route('surat-jalan.data.delete') }}",
                            data: {
                                id: id
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                alert("Data Surat Jalan berhasil dihapus!");
                                table.ajax.reload(); // Menggunakan reload pada tabel Anda
                            },
                            error: function(xhr, status, error) {
                                console.log('Error:', error);
                                console.log('Status:', status);
                                console.dir(xhr);
                                console.log('Response:', xhr.responseJSON);
                            }
                        });
                    } else {
                        // Jika pengguna menolak konfirmasi kedua
                        alert("Penghapusan dibatalkan.");
                    }
                }
            }
        </script>
    </x-slot:script>
</x-Layout.layout>
