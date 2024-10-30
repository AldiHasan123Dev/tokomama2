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
border-radius: 8px;
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

    <x-pajak.card>
        <x-slot:tittle>Buat Nomor Faktur</x-slot:tittle>
        <div class="grid grid-cols-3 gap-4">
            <label class="form-control w-96 max-w-xs">
                <div class="label">
                    <span class="label-text">Nomor awal faktur</span>
                </div>
                <input type="text" name="nomor" id="nomor-i" class="input input-bordered w-full max-w-xs rounded-md" />
            </label>
            <label class="form-control w-96 max-w-xs ">
                <div class="label">
                    <span class="label-text">Jumlah</span>
                </div>
                <input type="text" name="jumlah" id="jumlah-i" class="input input-bordered w-full max-w-xs rounded-md" />
                
            </label> 
            <div class="w-64 max-w xs">
                <div class="label">
                    <span class="label-text text-white">_</span>
                </div>
                <button class="btn bg-green-500 text-white font-semibold" id="generate">Generate</button>
            </div>
        </div>
    </x-pajak.card>

    <x-pajak.card>
        <x-slot:tittle>Nomor Faktur Tersedia</x-slot:tittle>
          <button id="delete-nsfp-all" class="btn bg-red-500 w-56 text-white font-semibold mb-3 self-end">Hapus Semua NSFP</button>
        <div class="overflow-x-auto">
            <table class="table" id="table-available">
              <!-- head -->
              <thead>
                <tr>
                    <th>No.</th>
                    <th>NSFP</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          
    </x-pajak.card>

    <x-pajak.card>
        <x-slot:tittle>Faktur Pajak Invoice</x-slot:tittle>
        <div class="overflow-x-auto">
            <table class="table" id="table-done">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>NSFP</th>
                  <th>Inovice</th>
                  <th>Keterangan</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
    </x-pajak.card>

    <x-slot name="script">
      <script>
        // table available invoice
          let table = $('#table-available').DataTable({
              pageLength: 100,
              ajax:{
                  url: "{{ route('nsfp.data') }}",
                  dataSrc: "data",
                   // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              },
              columns: [
                { data: 'DT_RowIndex', name: 'number'},
                { data: 'nomor', name: 'nomor' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'aksi', name: 'aksi' },
                { data: 'id', name: 'id', visible:false},
            ]
          });

          //delete all nsfp
          $('#delete-nsfp-all').click(function(e) {
            if(confirm('Apakah anda yakin?')) {
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{ route('nsfp.delete-all') }}",
                success: function(response) {
                  alert('Data berhasil di hapus semua');
                  table.ajax.reload();
                }
              })
            }
          })

          // Generate nomor faktur
        $('#generate').click(function(e) {
          var data = $('#jumlah-i').val();
          console.log(data);
          if(confirm('are you sure?')) {
            $.ajax({
              headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: "POST",
              url: "{{ route('api.nsfp.generate') }}",
              data: {
                nomor:$('#nomor-i').val(),
                jumlah:$('#jumlah-i').val()
              },
              success: function(response) {
                table.ajax.reload();
              }
            })
          }
        })


        // table invoice with nomor faktur
        let tableDone = $(`#table-done`).DataTable({
          pageLength: 100,
          ajax: {
            url: "{{ route('nsfp.done') }}",
            dataSrc: "data",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
          },
          columns: [
            
            { data: 'DT_RowIndex', name: 'number'},
            { data: 'id', name: 'id', visible:false},
            { data: 'nomor', name: 'nomor'},
            { data: 'invoice', name: 'invoice'},
            { data: 'keterangan', name: 'keterangan'}
          ]
        });

        function getDataNSFP(id, nomor, keterangan){
          $('#dialog').html(`<dialog id="my_modal_1" class="modal">
              <div class="modal-box  w-11/12 max-w-2xl pl-10 py-9 ">
              <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-bold">Edit Data</h3>
                <form action="{{route('nsfp.edit')}}" method="post">
                  @csrf
                  <input type="hidden" name="id" value="${id}" class="border-none" />
                  <label class="form-label"> Nomor</label>
                    <input type="text" name="nomor" value="${nomor}" class="input-field" />
                  <label class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" value="${keterangan}" class="input-field" />
                  <button type="submit" class="submit-button">Edit</button>
                </form>
              </div>
            </dialog>`);
          my_modal_1.showModal();
        }

        function deleteData(id) {
          if (confirm('Apakah anda ingin menghapus data ini?')) 
            {
                $.ajax
                ({
                    method: 'post',
                    url: "{{ route('nsfp.delete') }}",
                    data: {id: id},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function(response) 
                    {
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
    </x-slot>
</x-Layout.layout>