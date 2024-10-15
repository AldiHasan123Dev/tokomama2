<x-Layout.layout>
    <div id="dialog"></div>

    <x-master.card-master>
        <x-slot:tittle>Data COA</x-slot:tittle>
        <div class="overflow-x-auto">
            <table class="table" id="table-coa">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No Akun</th>
                        <th>Nama Akun</th>
                        <th>Status</th>
                        <th>Kategori LR</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </x-master.card-master>

    <x-master.card-master>
            <x-slot:tittle>Menambah Data COA</x-slot:tittle>
            <form action="{{ route('jurnal.coa.store') }}" method="post" class="grid grid-cols-3 gap-5">
                @csrf
                <label class="form-control w-full max-w-xs col-start-1">
                    <div class="label">
                        <span class="label-text">No. Akun <span class="text-red-500">*</span></span>
                    </div>
                    <input type="text" placeholder="No. Akun" name="no_akun" class="input input-bordered w-full max-w-xs rounded-md" required />
                </label>
                <label class="form-control w-full max-w-xs col-start-2">
                    <div class="label">
                        <span class="label-text">Nama Akun <span class="text-red-500">*</span></span>
                    </div>
                    <input type="text" placeholder="Nama Akun" name="nama_akun" class="input input-bordered w-full max-w-xs rounded-md" required />
                </label>
                <label class="input border flex items-center gap-2 mt-3">
                    Status:
                    <select name="status" class="select select-sm select-bordered w-full max-w-xs">
                        <option disabled selected>Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="non-aktif">Non-Aktif</option>
                    </select>
                </label>
                <label class="input border flex items-center gap-2 mt-3">
                    Tabel:
                    <select name="tabel" class="select select-sm select-bordered w-full max-w-xs">
                        <option disabled selected></option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                        <option value="G">G</option>
                    </select>
                </label>
                <div class="col-span-3 mt-8 text-center">
                    <button type="submit" class="btn text-semibold text-white bg-green-500 w-1/3 mx-auto">Simpan Data COA</button>
                </div>
            </form>
        </x-master.card-master>


    <x-slot:script>
        <script>
            let table = $('#table-coa').DataTable({
                pageLength: 100,
                ajax: {
                    url: "{{ route('jurnal.coa.data') }}",
                    type: "GET"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'number' },
                    { data: 'no_akun', name: 'no_akun' },
                    { data: 'nama_akun', name: 'nama_akun' },
                    { data: 'status', name: 'status' },
                    { data: 'tabel', name: 'tabel' },
                    { data: 'aksi', name: 'aksi' },
                    { data: 'id', name: 'id', visible: false }
                ]
            });

            function getData(id, no_akun, nama_akun, status, tabel) {
                $('#dialog').html(`
                    <dialog id="my_modal_6" class="modal">
                        <div class="modal-box w-11/12 max-w-2xl pl-10 py-9 ">
                            <form method="dialog">
                                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                            </form>
                            <h3 class="text-lg font-bold">Edit Data COA</h3>
                            <form action="{{ url('/coa') }}/${id}" method="post">
                                @csrf
                                @method('put')
                                <input type="hidden" name="id" value="${id}" class="border-none" />
                                <label class="input border flex items-center gap-2 mt-3">
                                    No Akun :
                                    <input type="text" name="no_akun" value="${no_akun}" class="border-none" />
                                </label>
                                <label class="input border flex items-center gap-2 mt-3">
                                    Nama Akun :
                                    <input type="text" name="nama_akun" value="${nama_akun}" class="border-none" />
                                </label>
                                <label class="input border flex items-center gap-2 mt-3">
                                    Status:
                                    <select name="status" class="select select-sm select-bordered w-full max-w-xs">
                                        <option selected>${status}</option>
                                        <option value="aktif">aktif</option>
                                        <option value="non-aktif">non-aktif</option>
                                    </select>
                                </label>
                                <label class="input border flex items-center gap-2 mt-3">
                                    Tabel:
                                    <select name="tabel" class="select select-sm select-bordered w-full max-w-xs">
                                        <option selected></option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                        <option value="F">F</option>
                                        <option value="G">G</option>
                                    </select>
                                </label>

                                <button type="submit" class="btn bg-green-400 text-white font-semibold w-72 mt-4">Edit</button>
                            </form>
                        </div>
                    </dialog>
                `);
                my_modal_6.showModal();
            }

            function deleteData(id) {
                if (confirm('Apakah anda ingin menghapus data ini?')) {
                    $.ajax({
                        method: 'DELETE',
                        url: "{{ url('coa') }}" + "/" + id,
                        data: { id: id },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            alert('Data COA berhasil dihapus!');
                            table.ajax.reload();
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
