<x-Layout.layout>

    <x-jurnal.card-jurnal>
        <x-slot:tittle>Data Template Jurnal</x-slot:tittle>
        <div class="grid grid-cols-6 gap-4">
            <a href="{{ route('jurnal.template-jurnal.create') }}"><button class="col-span-1 col-end-4 btn btn-success w-40 self-end font-semibold text-white">Buat Template</button></a>
        </div>
        <hr>
        <div class="overflow-x-auto">
            <table class="display" id="table-templateJurnal">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nama</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
    </x-jurnal.card-jurnal>

    <x-slot:script>
        <script>
          let table = $('#table-templateJurnal').DataTable({
            pageLength: 100,
            columnDefs: [
              {"className": "dt-center"}
            ],
            ajax: {
              url: "{{route('jurnal.template-jurnal.data')}}",
              type: "GET"
            },
            columns: [
                { data: 'DT_RowIndex', name: 'number'},
                { data: 'nama', name: 'nama template' },
                { data: 'aksi', name: 'aksi' },
                { data: 'id', name: 'id', visible:false},
            ]
          })

          function getData(id, nama) {
            $.ajax
                ({
                    method: 'post',
                    url: "{{ route('jurnal.template-jurnal.edit') }}",
                    data: {
                      id: id,
                      nama: nama
                    },
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function(response) 
                    {
                        console.log('hehe');
                    },
                    error: function(xhr, status, error) 
                    {
                        console.log('Error:', error);
                        console.log('Status:', status);
                        console.dir(xhr);
                    }
                })
          }

          function deleteData(id) {
            if (confirm('Apakah anda ingin menghapus data ini?')) 
            {
                $.ajax
                ({
                    method: 'post',
                    url: "{{ route('jurnal.template-jurnal.delete') }}",
                    data: {id: id},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function(response) 
                    {
                        alert("Nama template berhasil dihapus!");
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