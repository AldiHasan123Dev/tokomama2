<x-Layout.layout>
    <div id="dialog"></div>

    <x-master.card-master>
        <x-slot:tittle>Data Satuan</x-slot:tittle>
        <div class="overflow-x-auto">
          <table class="table" id="table-satuan">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama Satuan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </x-master.card-master>
    
    <x-master.card-master>
        <x-slot:tittle>Tambah Satuan</x-slot:tittle>
        <form action="{{ route('satuan.store') }}" method="post" class="grid grid-cols-2">
          @csrf
          <label class="form-control w-full max-w-xs col-start-1">
            <div class="label">
              <span class="label-text">Nama Satuan <span class="text-red-500">*</span></span>
            </div>
            <input type="text" placeholder="Nama" name="nama_satuan" class="input input-bordered w-full max-w-xs rounded-md"
              required />  
          </label>
          <button type="submit" class="btn w-56 p-4 mt-8 text-semibold text-white bg-green-500 col-start-2 ">Simpan Data
            Satuan</button>
        </form>
      </x-master.card-master>

      <x-slot:script>
        <script>
          let table = $('#table-satuan').DataTable({
            pageLength: 100,
            ajax: {
              url: "{{route('master.satuan.data')}}",
              type: "GET"
            },
            columns: [
                { data: 'DT_RowIndex', name: 'number'},
                { data: 'nama_satuan', name: 'nama satuan' },
                { data: 'aksi', name: 'aksi' },
                { data: 'id', name: 'id', visible:false},
            ]
          })

          function getData(id, nama_satuan) {
            $('#dialog').html(`<dialog id="my_modal_6" class="modal">
              <div class="modal-box  w-11/12 max-w-2xl pl-10 py-9 ">
              <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-bold">Edit Data</h3>
                <form action="{{url('/master/satuan')}}/${id}" method="post">
                  @csrf
                  @method('put')
                  <input type="hidden" name="id" value="${id}" class="border-none" />
                  <input type="hidden" name="id" value="${id}" class="border-none" />
                  <label class="input border flex items-center gap-2 mt-3">
                    Nama Satuan :
                    <input type="text" name="nama_satuan" value="${nama_satuan}" class="border-none" />
                  </label>
                  <button type="submit" class="btn bg-green-400 text-white font-semibold w-72 mt-4">Edit</button>
                </form>
              </div>
            </dialog>`);
            my_modal_6.showModal();
          }

          function deleteData(id) {
            if (confirm('Apakah anda ingin menghapus data ini?')) 
            {
                $.ajax
                ({
                    method: 'DELETE',
                    url: "{{ url('master/satuan') }}"+"/"+id,
                    data: {id: id},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function(response) 
                    {
                        alert('Data Master Satuan berhasil dihapus!');
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