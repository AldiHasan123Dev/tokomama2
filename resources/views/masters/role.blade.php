<x-Layout.layout>
        <style>
            ul {
            list-style: none;
            padding: 0;
            margin: 0;
            }

            .parent {
            margin-top: 20px;
            }

            .parent label {
            font-weight: bold;
            }

            .child-checkbox {
            margin-left: 20px;
            }

        </style>
    <x-master.card-master>
        <x-slot:button>
            <form action="{{ route('role.update', $role) }}" method="post" id="form">
                @csrf
                @method('PUT')
                <input type="hidden" name="sub_menu_id" id="sub_menu_id">
                <div class="flex gap-2">
                    <input type="text" name="name" id="name" class="form-control" placeholder="Nama Role" value="{{ $role->name }}" required>
                    <button type="submit" class="btn bg-green-400 text-white font-semibold w-72 mt-4">Simpan</button>
                </div>
            </form>
            </x-slot:button>
            <x-slot:tittle>MENU ROLE : {{ $role->name }}</x-slot:tittle>
            <ul class="parent">
                @foreach ($sub_menu as $menu)
                <li style="float: left">
                    <label for="parent-{{ $menu->first()->menu->id }}">{{ $menu->first()->menu->title }}</label>
                    <ul>
                        @foreach ($menu as $item)
                        <li>
                            <input {{ $role->menu()->where('menu_id',$item->id)->first() ? 'checked' : '' }} type="checkbox" name="sub_menu_id[]" id="child-{{ $item->id }}" value="{{ $item->id }}" class="child-checkbox" />
                            <label for="child-{{ $item->id }}">{{ $item->title }}</label>
                        </li>
                        @endforeach
                    </ul>
                </li>
                @endforeach
            </ul>
    </x-master.card-master>
    <x-slot:script>
        <script>
            $('#form').submit(function (e) { 
                e.preventDefault();
                var ids = $("input:checkbox:checked").map(function(){
                    return $(this).val();
                }).get();
                $('#sub_menu_id').val(ids);
                this.submit();
            });
        </script>
    </x-slot:script>
</x-Layout.layout>