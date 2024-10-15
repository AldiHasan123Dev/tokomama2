<x-Layout.layout>
    <div id="dialog"></div>
    <x-master.card-master>
        <x-slot:tittle>Table Supllier</x-slot:tittle>
        <div class="overflow-x-auto">
            <table class="table" id="table-supplier">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nama</th>
                  <th>NPWP</th>
                  <th>Email</th>
                  <th>No.Telp</th>
                  <th>Alamat</th>
                  <th>Kota</th>
                  <th>Alamat NPWP</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
    </x-master.card-master>

    <x-master.card-master>
        <x-slot:tittle>Menambah Data Supplier</x-slot:tittle>
        <form action="{{ route('master.supplier.add') }}" method="post" class="grid grid-cols-4 gap-5">
          @csrf
          <label class="form-control w-full max-w-xs col-start-1">
            <div class="label">
              <span class="label-text">Nama Supplier <span class="text-red-500">*</span></span>
            </div>
            <input type="text" placeholder="Nama" name="nama" class="input input-bordered w-full max-w-xs rounded-md"
              required />
              
          </label>
          <label class="form-control w-full max-w-xs col-start-2">
            <div class="label">
              <span class="label-text">NPWP <span class="text-red-500">*</span></span>
            </div>
            <input type="text" placeholder="NPWP" name="npwp" class="input input-bordered w-full max-w-xs rounded-md"
              required />
          </label>
          <label class="form-control w-full max-w-xs col-start-3">
            <div class="label">
              <span class="label-text">Nama NPWP <span class="text-red-500">*</span></span>
            </div>
            <input type="text" placeholder="Nama NPWP" name="nama_npwp"
              class="input input-bordered w-full max-w-xs rounded-md" required />
          </label>
          <label class="form-control w-full max-w-xs col-start-4">
            <div class="label">
              <span class="label-text">Email <span class="text-red-500">*</span></span>
            </div>
            <input type="text" placeholder="Email" name="email" class="input input-bordered w-full max-w-xs rounded-md"
              />
          </label>
    
          <label class="form-control w-full max-w-xs col-start-1">
            <div class="label">
              <span class="label-text">No Telp <span class="text-red-500">*</span></span>
            </div>
            <input type="text" placeholder="Nomor Telepon" name="no_telp"
              class="input input-bordered w-full max-w-xs rounded-md" required />
          </label>
          <label class="form-control w-full max-w-xs col-start-2">
            <div class="label">
              <span class="label-text">Alamat <span class="text-red-500">*</span></span>
            </div>
            <input type="text" placeholder="Alamat" name="alamat" class="input input-bordered w-full max-w-xs rounded-md"
              required />
          </label>
          
          <label class="form-control w-full max-w-xs col-start-3">
            <div class="label">
              <span class="label-text">Kota <span class="text-red-500">*</span></span>
            </div>
            <input type="text" placeholder="Kota" name="kota" class="input input-bordered w-full max-w-xs rounded-md"
              required />
          </label>
          <label class="form-control w-full max-w-xs col-start-4">
            <div class="label">
              <span class="label-text">Alamat NPWP <span class="text-red-500">*</span></span>
            </div>
            <input type="text" placeholder="Alamat NPWP" name="alamat_npwp"
              class="input input-bordered w-full max-w-xs rounded-md" required />
          </label>
          <span class="mt-1"><span class="text-red-500">*</span>) Jika tidak ada silahkan isi "-"</span>
          <button type="submit" class="btn p-4 mt-3 text-semibold text-white bg-green-500 col-span-4">Simpan Data
            Supplier</button>
        </form>
      </x-master.card-master>

    <x-slot:script>
        <script>
            let table = $('#table-supplier').DataTable({
            pageLength: 100,
            ajax: {
              url: "{{route('master.supplier.datatable')}}",
              
              data:{
                _token: "{{csrf_token()}}"
              }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'number'},
                { data: 'nama', name: 'nama' },
                { data: 'npwp', name: 'npwp' },
                { data: 'email', name: 'email' },
                { data: 'no_telp', name: 'no_telp' },
                { data: 'alamat', name: 'alamat' },
                { data: 'kota', name: 'kota' },
                { data: 'alamat_npwp', name: 'alamat_npwp' },
                { data: 'aksi', name: 'aksi' },
                { data: 'id', name: 'id', visible:false},
            ]
          })

          function getData(id, nama, npwp, nama_npwp, email, no_telp, alamat, alamat_npwp, kota ) 
          {
            // alert(nama);
            $('#dialog').html(`<dialog id="my_modal_5" class="modal">
              <div class="modal-box w-11/12 max-w-2xl pl-10 py-9">
              <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
              </form>
                <h3 class="text-lg font-bold">Edit Data</h3>
                <form action="{{ route('master.supplier.edit') }}" method="post">
                  @csrf
                  <input type="hidden" name="id" value="${id}" class="border-none" />
                  <label class="input border flex items-center gap-2 mt-3">
                    Name :
                    <input type="text" name="nama" value="${nama}" class="border-none" />
                  </label>
                  <label class="input border flex items-center gap-2 mt-3">
                    NPWP :
                    <input type="text" name="npwp" value="${npwp}" class="border-none" />
                  </label>
                  <label class="input border flex items-center gap-2 mt-3">
                    Nama NPWP :
                    <input type="text" name="nama_npwp" value="${nama_npwp}" class="border-none" />
                  </label>
                  <label class="input border flex items-center gap-2 mt-3">
                    Email :
                    <input type="text" name="email" value="${email}" class="border-none" />
                  </label>
                  <label class="input border flex items-center gap-2 mt-3">
                    No.Telp :
                    <input type="text" name="no_telp" value="${no_telp}" class="border-none" />
                  </label>
                  <label class="input border flex items-center gap-2 mt-3">
                    Alamat :
                    <input type="text" name="alamat" value="${alamat}" class="border-none" />
                  </label>
                  <label class="input border flex items-center gap-2 mt-3">
                    Kota :
                    <input type="text" name="kota" value="${kota}" class="border-none" />
                  </label>
                  <label class="input border flex items-center gap-2 mt-3">
                    Alamat NPWP :
                    <input type="text" name="alamat_npwp" value="${alamat_npwp}" class="border-none" />
                  </label>
                  <button type="submit" class="btn bg-green-400 text-white font-semibold w-72 mt-2">Edit</button>
                </form>
              </div>
            </dialog>`);
            my_modal_5.showModal();
            // alert(id, email) 
          }

          function deleteData(id) 
          {
            if (confirm('Are you sure you want to delete this data?'))
            {
              $.ajax
              ({
                method: 'post',
                url: "{{ route('master.supplier.delete') }}",
                data: {id: id},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) 
                {
                  alert("Data Master Supplier berhasil dihapus!")
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