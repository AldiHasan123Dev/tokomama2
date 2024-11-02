<x-Layout.layout>
    <div id="dialog"></div>
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
          background-color: #e0a50f;
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
                  <label class="form-label"> Nama Satuan :</label>
                    <input type="text" name="nama_satuan" value="${nama_satuan}" class="input-field" />
                  <button type="submit" class="submit-button">Edit</button>
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