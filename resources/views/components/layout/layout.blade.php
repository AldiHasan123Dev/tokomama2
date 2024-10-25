<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>SB | Sarana Bahagia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Tailwind Admin & Dashboard Template" name="description">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="Themesbrand" name="author">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('/assets/img/logo_sb.svg') }}">
    <!-- Layout config Js -->
    <!-- Icons CSS -->
    @vite('resources/css/app.css')

    <!-- ui jqgrid -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/css/ui.jqgrid.min.css">

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/tailwind2.css')}}">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- data table -->
    <link rel="stylesheet" type="text/css" href="https:///cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- jqgrid -->
    <!-- <script src="js/jquery.jqGrid.min.js"  type="text/ecmascript"></script> -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css"
        integrity="sha512-pTaEn+6gF1IeWv3W1+7X7eM60TFu/agjgoHmYhAfLEU8Phuf6JKiiE8YmsNC0aCgQv4192s4Vai8YZ6VNM6vyQ=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
        integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    ></script>

</head>

<body data-mode="light" data-sidebar-size="lg" class="group">

    <x-Layout.sidebar />

    <x-Layout.navbar />

    <div class="main-content group-data-[sidebar-size=sm]:ml-[70px]">
        <div class="page-content">
            <div class="my-5">
                @if(session('error'))
                <div role="alert" class="alert alert-error mb-5">
                    <i class="fa-regular fa-circle-xmark"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                @elseif(session('success'))
                <div role="alert" class="alert alert-success mb-5">
                    <i class="fa-regular fa-circle-check text-xl"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                @endif
            </div>
            {{ $slot }}
        </div>
    </div>


    <script src="{{ asset('/assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>
    <script src="{{ asset('/assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('/assets/libs/metismenujs/metismenujs.min.js') }}"></script>
    <script src="{{ asset('/assets/libs/simplebar/simplebar.min.js') }}"></script>

    <!-- plugin js -->
    <!-- <script src="assets/libs/fullcalendar/index.global.min.js"></script> -->

    <!-- calendar init -->
    <!-- <script src="assets/js/pages/calendar.init.js"></script>  -->

    <script src="{{ asset('/assets/js/app.js') }}"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    {{-- <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{ $script ?? '' }}
</body>

</html>
