<x-Layout.layout>
  <div id="dialog"></div>

  <x-master.card-master>
    <x-slot:tittle>Data Nomor Polisi</x-slot:tittle>
    <div class="overflow-x-auto">
      <table class="table" id="table-nopol">
        <thead>
          <tr>
            <th>#</th>
            <th>Nopol</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </x-master.card-master>

  <x-master.card-master>
    <x-slot:tittle>Menambah Data Nomor Polisi</x-slot:tittle>
    <form action="{{route('master.nopol.add')}}" method="post" class="grid grid-cols-3 ">
      @csrf
      <label class="form-control w-full max-w-xs col-start-1">
        <div class="label">
          <span class="label-text">nopol <span class="text-red-500">*</span></span>
        </div>
        <input type="text" placeholder="Nomor Polisi" name="nopol"
          class="input input-bordered w-full max-w-xs rounded-md" required />
      </label>
      <div class="mt-9">
        <button type="submit" class="btn text-semibold text-white bg-green-500">Simpan Data Customer</button>
      </div>

    </form>
  </x-master.card-master>

  <x-slot:script>
    <script>
      let table = $('#table-nopol').DataTable({
            pageLength: 100,
            ajax: {
              url: "{{route('master.nopol.list')}}",
              
              data:{
                _token: "{{csrf_token()}}"
              }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'number'},
                { data: 'nopol', name: 'nopol' },
                { data: 'status', name: 'status' },
                { data: 'aksi', name: 'aksi' },
                { data: 'id', name: 'id', visible:false},
            ]
          })

          function ubahStatus(id, status) {
            if (confirm('Apakah anda ingin mengubah status data ini?')) 
            {
                $.ajax
                ({
                    method: 'post',
                    url: "{{ route('master.nopol.editstatus') }}",
                    data: {
                      id: id,
                      status: status
                    },
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function(response) 
                    {
                        alert("Status Data Master Nomor Polisi berhasil diubah!");
                        table.ajax.reload();
                    },
                    error: function(xhr, status, error) 
                    {
                        console.log('Error:', error);
                        console.log('Status:', status);
                        console.dir(xhr);
                    }
                })
            }
          }

          function getData(id, nopol) {
            $('#dialog').html(`<dialog id="my_modal_7" class="modal modal-bottom sm:modal-middle">
              <div class="modal-box  w-11/12 max-w-2xl pl-10 py-9">
              <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-bold">Edit Data</h3>
                <form action="{{route('master.nopol.edit')}}" method="post">
                  @csrf
                  <input type="hidden" name="id" value="${id}" class="border-none" />
                  <label class="input border flex items-center gap-2 mt-3">
                    Kode Objek :
                    <input type="text" name="nopol" value="${nopol}" class="border-none" />
                  </label>
                  <button type="submit" class="btn bg-green-400 text-white font-semibold w-72 mt-4">Edit</button>
                </form>
              </div>
            </dialog>`);
            my_modal_7.showModal();
          }

          function deleteData(id) {
            if (confirm('Apakah anda ingin menghapus data ini?')) 
            {
                $.ajax
                ({
                    method: 'post',
                    url: "{{ route('master.nopol.delete') }}",
                    data: {id: id},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function(response) 
                    {
                        alert("Status Data Master Nomor Polisi berhasil dihapus!");
                        table.ajax.reload();
                    },
                    error: function(xhr, status, error) 
                    {
                        console.log('Error:', error);
                        console.log('Status:', status);
                        console.dir(xhr);
                    }
                })
            }
          }
    </script>
  </x-slot:script>
</x-Layout.layout>