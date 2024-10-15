
<x-Layout.layout>
    <div id="dialog"></div>
    
  <div id="modal_user"></div>

    <x-master.card-master>
      <x-slot:button>
        <form action="{{ route('role.store') }}" method="post" class="flex gap-2">
          @csrf
          <input type="text" name="name" id="name" class="form-control" placeholder="Nama Role" required>
          <button type="submit" class="btn bg-green-500 text-white p-3 font-semibold w-40">Tambah Role</button>
        </form>
      </x-slot:button>
        <x-slot:tittle>Data Role & Menu</x-slot:tittle>
        <div class="overflow-x-auto">
            <table class="table" id="table-user">
              <thead>
                <tr>
                  <th>#</th>
                  <th style="width: 150px">Role</th>
                  <th>Menu</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($roles as $item)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>
                      <div class="flex gap-2 flex-wrap">
                        @foreach ($item->menu as $menu)
                          <span class="px-2 py-1 rounded bg-slate-200">{{ $menu->menu->title }}</span>
                      @endforeach
                      </div>
                    </td>
                    <td>
                      <div class="flex gap-2">
                        <a href="{{ route('role.show', $item) }}" class="btn bg-blue-400 text-white btn-sm">Edit</a>
                        <form action="{{ route('role.destroy', $item) }}" method="post">
                          @csrf
                          @method('delete')
                          <button type="submit" onclick="return confirm('Apakah anda yakin?')" class="btn bg-red-500 text-white btn-sm">Delete</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
    </x-master.card-master>

    <x-slot:script>
        <script>
            
        </script>
    </x-slot:script>
</x-Layout.layout>