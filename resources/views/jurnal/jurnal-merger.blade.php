<x-Layout.layout>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Jurnal Merger</x-slot:tittle>
        <div class="overflow-x-auto">
            <form action="{{ route('jurnal.jurnal-merger') }}" method="post">
                @csrf
                <div class="grid grid-cols-3 gap-5 my-3">
                    <div>
                        <label for="jurnal_awal">No. Jurnal Awal</label>
                        <select class="js-example-basic-single select w-full" name="jurnal_awal" id="jurnal_awal">
                                <option value="" selected disabled>Pilih No. Jurnal</option>
                            @foreach($jurnal as $j)
                                <option value="{{ $j->nomor }}">{{ $j->nomor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="jurnal_tujuan">No. Jurnal Tujuan</label>
                        <select class="js-example-basic-single select w-full" name="jurnal_tujuan" id="jurnal_tujuan">
                            <option value="" selected disabled>Pilih No. Jurnal</option>
                            @foreach($jurnal as $j)
                                <option value="{{ $j->nomor }}">{{ $j->nomor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button class="btn bg-green-500 rounded-xl hover:bg-green-700 text-white w-1/2" type="submit">Merge</button>
                    </div>
                </div>
            </form>
        </div>
    </x-keuangan.card-keuangan>

    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>

</x-Layout.layout>
