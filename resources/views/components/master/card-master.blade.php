<style>
    .custom-title {
        font-size: 1rem; /* Ukuran font */
        font-weight: bold; /* Menambahkan efek tebal */
        color: #46b40b; /* Warna biru */
        text-align: center; /* Rata tengah */
        margin: 5px 0; /* Jarak atas dan bawah */
        padding: 5px; /* Jarak dalam elemen */
        border: 1px #08d216; /* Border dengan warna biru */
        border-radius: 5px; /* Sudut membulat */
        background-color: #f9fbff; /* Latar belakang judul */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Bayangan di bawah elemen */
    }
</style>
<div class="card w-full bg-base-100 shadow-xl overflow-hidden">
        <div class="card-body">
            <div class="flex justify-between">
                <h2 class="custom-title">{{$tittle}}</h2>
                {{ $button ?? '' }}
            </div>
            <hr>
            {{$slot}}
        </div>
    </div>