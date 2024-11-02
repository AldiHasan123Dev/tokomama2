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
  <div id="dialog"></div>

  <x-master.card-master>
    <x-slot:tittle>Data Ekspedisi</x-slot:tittle>
    <div class="overflow-x-auto">
      <table class="table" id="table-ekspedisi">
        <thead>
          <tr>
            <th>#</th>
            <th>Nama</th>
            <th>PIC</th>
            <th>Email</th>
            <th>No Telp</th>
            <th>Alamat</th>
            <th>Kota</th>
            <th>FAX</th>
            <th>Aksi</th>
            <th>ID</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </x-master.card-master>

  <x-master.card-master>
    <x-slot:tittle>Menambah Data Ekspedisi</x-slot:tittle>
    <form action="{{route('ekspedisi.store')}}" method="post" class="grid grid-cols-4 gap-5">
      @csrf
      <label class="form-control w-full max-w-xs col-start-1">
        <div class="label">
          <span class="label-text">Nama</span>
        </div>
        <input type="text" placeholder="Nama Expedisi" name="nama"
          class="input input-bordered w-full max-w-xs rounded-md" />
      </label>
      <label class="form-control w-full max-w-xs col-start-2">
        <div class="label">
          <span class="label-text">PIC</span>
        </div>
        <input type="text" placeholder="PIC" name="pic" class="input input-bordered w-full max-w-xs rounded-md" />
      </label>
      <label class="form-control w-full max-w-xs col-start-3">
        <div class="label">
          <span class="label-text">Alamat</span>
        </div>
        <input type="text" placeholder="Alamat" name="alamat" class="input input-bordered w-full max-w-xs rounded-md" />
      </label>
      <label class="form-control w-full max-w-xs col-start-4">
        <div class="label">
          <span class="label-text">Kota</span>
        </div>
        <input type="text" placeholder="Kota" name="kota" class="input input-bordered w-full max-w-xs rounded-md" />
      </label>
      <label class="form-control w-full max-w-xs col-start-1">
        <div class="label">
          <span class="label-text">Email</span>
        </div>
        <input type="text" placeholder="Email" name="email" class="input input-bordered w-full max-w-xs rounded-md" />
      </label>
      <label class="form-control w-full max-w-xs col-start-2">
        <div class="label">
          <span class="label-text">No Telp</span>
        </div>
        <input type="text" placeholder="No telp" name="no_telp"
          class="input input-bordered w-full max-w-xs rounded-md" />
      </label>
      <label class="form-control w-full max-w-xs col-start-3">
        <div class="label">
          <span class="label-text">FAX</span>
        </div>
        <input type="text" placeholder="FAX" name="fax" class="input input-bordered w-full max-w-xs rounded-md" />
      </label>
      <div class="col-start-4 mt-9">
        <button type="submit" class="btn text-semibold text-white bg-green-500 w-full">Simpan Data Ekspedisi</button>
      </div>

    </form>
  </x-master.card-master>

  <x-slot:script>
    <script>
      let table = $('#table-ekspedisi').DataTable({
            pageLength: 100,
            ajax: {
              url: "{{route('ekspedisi.data')}}",
              method: 'POST',
              data:{
                _token: "{{csrf_token()}}"
              }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'number'},
                { data: 'nama', name: 'nama' },
                { data: 'pic', name: 'pic' },
                { data: 'email', name: 'email' },
                { data: 'no_telp', name: 'no_telp' },
                { data: 'alamat', name: 'alamat' },
                { data: 'kota', name: 'kota' },
                { data: 'fax', name: 'fax' },
                { data: 'aksi', name: 'aksi' },
                { data: 'id', name: 'id', visible:false},
            ]
          })

          function getData(id, nama, emaul, no_telp, alamat, kota, pic, fax) {
            $('#dialog').html(`<dialog id="my_modal_6" class="modal">
              <div class="modal-box  w-11/12 max-w-2xl pl-10 py-9 ">
              <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-bold">Edit Data Ekspedisi</h3>
                <form action="{{url('master/ekspedisi')}}/${id}" method="post">
                  @csrf
                  @method('put')
                  <input type="hidden" name="id" value="${id}" class="border-none" />
                  <label class="form-label">Nama</label>
                    <input type="text" placeholder="Nama Barang" value="${nama}" name="nama" class="input-field" />
                  <label class="form-label">PIC</label>
                    <input type="text" placeholder="PIC" value="${pic}" name="pic" class="input-field" />
                  <label class="form-field">Alamat</label>
                    <input type="text" placeholder="Alamat" value="${alamat}" name="alamat" class="input-field" />
                  <label class="form-label">Kota</label>
                    <input type="text" placeholder="Kota" value="${kota}" name="kota" class="input-field" />
                  <label class="form-label">Email</label>
                    <input type="text" placeholder="Email" value="${emaul}" name="email" class="input-field" />
                  <label class="form-label">No Telp</label>
                    <input type="text" placeholder="No telp" value="${no_telp}" name="no_telp" class="input-field" />
                  <label class="form-label">FAX</label>
                    <input type="text" placeholder="FAX" value="${fax}" name="fax" class="input-field" />
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
                    url: "{{ url('master/ekspedisi') }}"+"/"+id,
                    data: {id: id},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function(response) 
                    {
                        alert('Data Master Ekspedisi berhasil dihapus!');
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