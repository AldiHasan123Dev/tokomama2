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

        .submit-button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #e0a50f;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        .label-info {
            font-size: 12px;
            color: red;
        }
    </style>

    <div id="dialog"></div>

    <x-master.card-master>
        <x-slot:tittle>Data Alat Berat</x-slot:tittle>
        <div class="overflow-x-auto">
            <table id="table-barang" class="display compact" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </x-master.card-master>

    <x-master.card-master>
        <x-slot:tittle>Menambah Data Alat Berat</x-slot:tittle>
        <form action="{{ route('master.alat_berat.add') }}" method="post" class="grid grid-cols-3 gap-5">
            @csrf
            <label class="form-control w-full max-w-xs col-start-2">
                <div class="label">
                    <span class="label-text">Nama <span class="text-red-500">*</span></span>
                </div>
                <!-- id dan name konsisten: nama_alat -->
                <input id="nama_alat" type="text" placeholder="Nama Alat Berat" name="nama_alat"
                    class="input input-bordered w-full max-w-xs rounded-md" required />
            </label>
            <div class="col-span-3 mt-8 text-center">
                <button type="submit" class="btn text-semibold text-white bg-green-500 w-1/3 mx-auto">Simpan Data
                    Alat Berat</button>
            </div>
        </form>
    </x-master.card-master>

    <x-slot:script>
        <script>
            // Inisialisasi DataTable
            let table = new DataTable('#table-barang', {
                pageLength: 20,
                ajax: {
                    url: "{{ route('master.alat_berat.list') }}",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'number' },
                    { data: 'nama_alat', name: 'nama_alat' },
                    { data: 'aksi', name: 'aksi' },
                    { data: 'id', name: 'id', visible: false }
                ]
            });

            // Hapus handler redundant (input add sudah otomatis terisi)
            // $('#nama_alat').on('keyup', function() {
            //     $('#nama_alat').val(this.value);
            // });

            /**
             * Buka modal edit
             * @param {Number|String} id
             * @param {String} nama
             */
            function getData(id, nama) {
                // Buat markup dialog; gunakan backticks agar string multiline
                document.getElementById('dialog').innerHTML = `
                <dialog id="my_modal_6" class="modal">
                  <div class="modal-box w-11/12 max-w-2xl pl-10 py-9">
                    <form method="dialog">
                      <button type="button" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" id="close_modal_btn">✕</button>
                    </form>
                    <h3 class="text-lg font-bold">Edit Data Alat Berat</h3>
                    <form id="form-edit-alat" action="{{ route('master.alat_berat.edit') }}" method="post">
                      @csrf
                      <label class="form-label">Nama Alat :</label>
                      <input type="text" name="nama_alat" value="${nama}" class="input-field" required />
                      <input type="hidden" name="id" value="${id}" />
                      <div style="margin-top:16px;">
                        <button type="submit" class="submit-button">Edit</button>
                      </div>
                    </form>
                  </div>
                </dialog>`;

                // ambil element dialog yang baru dibuat lalu tampilkan
                const dialogEl = document.getElementById('my_modal_6');
                if (dialogEl) {
                    // jika browser mendukung dialog
                    if (typeof dialogEl.showModal === "function") {
                        dialogEl.showModal();
                    } else {
                        // fallback: tampilkan secara block (simple)
                        dialogEl.style.display = 'block';
                    }

                    // close button handler
                    const btnClose = document.getElementById('close_modal_btn');
                    if (btnClose) {
                        btnClose.addEventListener('click', function () {
                            if (typeof dialogEl.close === "function") {
                                dialogEl.close();
                            } else {
                                dialogEl.style.display = 'none';
                            }
                        });
                    }

                    // optional: setelah submit, reload table otomatis lewat event atau server response
                    // jika kamu ingin menggunakan AJAX submit edit, bisa ubah form submit di sini.
                } else {
                    console.error('Dialog element tidak ditemukan');
                }
            }

            // Hapus data dengan AJAX
            function deleteData(id) {
                if (!confirm('Apakah anda ingin menghapus data ini?')) return;

                $.ajax({
                    method: 'post',
                    url: "{{ route('master.alat_berat.delete') }}",
                    data: {
                        id: id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert("Data Master Barang berhasil dihapus!");
                        table.ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        console.error('Status:', status);
                        console.dir(xhr);
                        alert('Terjadi kesalahan saat menghapus data. Cek console untuk detail.');
                    }
                });
            }
        </script>
    </x-slot:script>
</x-Layout.layout>
