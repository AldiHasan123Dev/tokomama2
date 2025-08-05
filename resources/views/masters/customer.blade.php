<x-Layout.layout>

  <div id="satu"></div>
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
    <!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <x-slot:tittle>Table Customer</x-slot:tittle>
    <div class="overflow-x-auto">
      <table class="table" id="table-customer">
        <thead>
          <tr>
            <th>ID</th>
            <th>Sales</th>
            <th>ID Sales</th>
            <th>Nama</th>
            <th>NPWP</th>
            <th>TOP
            </th>
            <th>Email</th>
            <th>No.Telp</th>
            <th>Alamat</th>
            <th>Kota</th>
            <th>Nama NPWP</th>
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
    <x-slot:tittle>Menambah Data Customer</x-slot:tittle>
    <form action="{{route('master.customer.add')}}" method="post" class="grid grid-cols-4 gap-5">
      @csrf
      <label class="form-control w-full max-w-xs col-start-1">
        <div class="label">
          <span class="label-text">Nama Customer <span class="text-red-500">*</span></span>
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
          <span class="label-text">TOP <span class="text-red-500">*</span></span>
        </div>
        <input type="text" placeholder="Terms Of Payment" name="top"
          class="input input-bordered w-full max-w-xs rounded-md" required />
      </label>
          <label class="form-control w-full max-w-xs col-start-1">
        <div class="label">
          <span class="label-text">Sales <span class="text-red-500">*</span></span>
        </div>
        <select name="sales" id="sales" class="select2 input input-bordered w-full max-w-xs rounded-md" required>
          <option value="">-- Pilih Sales --</option>
          @foreach($sales as $sale)
            <option value="{{ $sale->id }}">{{ $sale->nama }}</option>
          @endforeach
        </select>
      </label>
      <label class="form-control w-full max-w-xs col-start-2">
        <div class="label">
          <span class="label-text">Email <span class="text-red-500">*</span></span>
        </div>
        <input type="text" placeholder="Email" name="email" class="input input-bordered w-full max-w-xs rounded-md"
          />
      </label>

      <label class="form-control w-full max-w-xs col-start-3">
        <div class="label">
          <span class="label-text">No Telp <span class="text-red-500">*</span></span>
        </div>
        <input type="text" placeholder="Nomor Telepon" name="no_telp"
          class="input input-bordered w-full max-w-xs rounded-md" required />
      </label>
      <label class="form-control w-full max-w-xs col-start-4">
        <div class="label">
          <span class="label-text">Alamat <span class="text-red-500">*</span></span>
        </div>
        <input type="text" placeholder="Alamat" name="alamat" class="input input-bordered w-full max-w-xs rounded-md"
          required />
      </label>
      
      <label class="form-control w-full max-w-xs col-start-2">
        <div class="label">
          <span class="label-text">Kota <span class="text-red-500">*</span></span>
        </div>
        <input type="text" placeholder="Kota" name="kota" class="input input-bordered w-full max-w-xs rounded-md"
          required />
      </label>
      <label class="form-control w-full max-w-xs col-start-3">
        <div class="label">
          <span class="label-text">Alamat NPWP <span class="text-red-500">*</span></span>
        </div>
        <input type="text" placeholder="Alamat NPWP" name="alamat_npwp"
          class="input input-bordered w-full max-w-xs rounded-md" required />
      </label>
      <span class="mt-5"><span class="text-red-500">*</span> Jika tidak ada silahkan isi "-"</span>
      <button type="submit" class="btn p-4 mt-3 text-semibold text-white bg-green-500 col-span-4">Simpan Data
        Customer</button>
    </form>
  </x-master.card-master>


  <x-slot:script>
    <script>
  $(document).ready(function() {
    $('#sales').select2({
      placeholder: "-- Pilih Sales --",
      allowClear: true,
      width: '100%' // agar cocok dengan Tailwind w-full
    });
  });
</script>

    <script>
       const salesList = @json($sales);
      let table = $('#table-customer').DataTable({
            pageLength: 100,
            ordering: false,
            ajax: {
              url: "{{route('master.customer.list')}}",
              
              data:{
                _token: "{{csrf_token()}}"
              }
            },
            columns: [
                { data: 'id', name: 'id'},
                { data: 'sales', name: 'sales' },
                { data: 'id_sales', name: 'id_sales' },
                { data: 'nama', name: 'nama' },
                { data: 'npwp', name: 'npwp' },
                { data: 'top', name: 'top'},
                { data: 'email', name: 'email' },
                { data: 'no_telp', name: 'no_telp' },
                { data: 'alamat', name: 'alamat' },
                { data: 'kota', name: 'kota' },
                { data: 'nama_npwp', name: 'nama_npwp' },
                { data: 'alamat_npwp', name: 'alamat_npwp' },
                { data: 'aksi', name: 'aksi' },
                { data: 'id', name: 'id', visible:false},
            ]
          })

          function getData(id, id_sales, nama, npwp, nama_npwp, email, no_telp, alamat, alamat_npwp, kota, top, salesName) 
{
  let salesOptions = '<option value="">-- Pilih Sales --</option>';

  salesList.forEach(s => {
    const selected = s.id == id_sales ? 'selected' : '';
    salesOptions += `<option value="${s.id}" ${selected}>${s.nama}</option>`;
  });

  $('#satu').html(`
    <dialog id="my_modal_5" class="modal">
      <div class="modal-box w-11/12 max-w-2xl pl-10 py-9">
        <form method="dialog">
          <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-lg font-bold">Edit Data Customer</h3>
        <form action="{{ route('master.customer.edit') }}" method="post">
          @csrf
          <input type="hidden" name="id" value="${id}" />

          <label class="form-label">Nama:</label>
          <input type="text" name="nama" value="${nama}" class="input-field" />

          <label class="form-label">NPWP:</label>
          <input type="text" name="npwp" value="${npwp}" class="input-field" />

          <label class="form-label">Nama NPWP:</label>
          <input type="text" name="nama_npwp" value="${nama_npwp}" class="input-field" />

          <label class="form-label">TOP:</label>
          <input type="text" name="top" value="${top}" class="input-field" />

          <label class="form-label">Sales:</label>
          <select name="sales" id="sales-select" class="input input-bordered w-full max-w-xs rounded-md select2">
            ${salesOptions}
          </select>

          <label class="form-label">Email:</label>
          <input type="text" name="email" value="${email}" class="input-field" />

          <label class="form-label">No. Telp:</label>
          <input type="text" name="no_telp" value="${no_telp}" class="input-field" />

          <label class="form-label">Alamat:</label>
          <input type="text" name="alamat" value="${alamat}" class="input-field" />

          <label class="form-label">Kota:</label>
          <input type="text" name="kota" value="${kota}" class="input-field" />

          <label class="form-label">Alamat NPWP:</label>
          <input type="text" name="alamat_npwp" value="${alamat_npwp}" class="input-field" />

          <button type="submit" class="submit-button">Edit</button>
        </form>
      </div>
    </dialog>
  `);

  my_modal_5.showModal();

  // Inisialisasi select2 (tanpa AJAX)
  $('#sales-select').select2({
    dropdownParent: $('#my_modal_5'),
    width: '100%'
  });
}


          function deleteData(id) 
          {
            if (confirm('Are you sure you want to delete this data?'))
            {
              $.ajax
              ({
                method: 'post',
                url: "{{ route('master.customer.delete') }}",
                data: {id: id},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) 
                {
                  alert("Data Master Customer berhasil dihapus!")
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