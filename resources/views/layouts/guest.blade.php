<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Tailwind -->
        <link rel="stylesheet" href="{{ asset('/assets/css/tailwind2.css') }}">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    </head>
    <body data-mode="light" data-sidebar-size="lg" class="group">
        <div class="container-fluid">
            <div class="h-screen md:overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-12 ">

                    {{ $slot }}

                    <div class="col-span-12 md:col-span-7 lg:col-span-8 xl:col-span-9">
                        <div class="h-screen bg-cover relative p-5 bg-[url('../images/auth-bg.jpg')]">
                            <div class="absolute inset-0 bg-violet-500/90"></div>

                            <ul class="absolute top-0 left-0 w-full h-full overflow-hidden bg-bubbles animate-square">
                                <li class="h-10 w-10 rounded-3xl bg-white/10 absolute left-[10%] "></li>
                                <li class="h-28 w-28 rounded-3xl bg-white/10 absolute left-[20%]"></li>
                                <li class="h-10 w-10 rounded-3xl bg-white/10 absolute left-[25%]"></li>
                                <li class="h-20 w-20 rounded-3xl bg-white/10 absolute left-[40%]"></li>
                                <li class="h-24 w-24 rounded-3xl bg-white/10 absolute left-[70%]"></li>
                                <li class="h-32 w-32 rounded-3xl bg-white/10 absolute left-[70%]"></li>
                                <li class="h-36 w-36 rounded-3xl bg-white/10 absolute left-[32%]"></li>
                                <li class="h-20 w-20 rounded-3xl bg-white/10 absolute left-[55%]"></li>
                                <li class="h-12 w-12 rounded-3xl bg-white/10 absolute left-[25%]"></li>
                                <li class="h-36 w-36 rounded-3xl bg-white/10 absolute left-[90%]"></li>
                            </ul>

                            <div class="flex items-center justify-center h-screen ">
                                <div class="w-full md:max-w-4xl lg:px-9">
                                    <div class="swiper login-slider">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                                <h3 class="mt-4 text-white text-22">“I feel confident imposing change on myself. It's a lot more progressing fun than looking back. That's why I ultricies enim at malesuada nibh diam on tortor neaded to throw curve balls.”</h3>
                                                <div class="flex pt-4 mt-6 mb-10">
                                                    <img src="assets/images/avatar-1.jpg" class="w-12 h-12 rounded-full" alt="...">
                                                    <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-2">
                                                        <h5 class="text-white font-size-18">Ilse R. Eaton</h5>
                                                        <p class="mb-0 text-white/50">Manager
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                                <h3 class="mt-4 text-white text-22">“I feel confident imposing change on myself. It's a lot more progressing fun than looking back. That's why I ultricies enim at malesuada nibh diam on tortor neaded to throw curve balls.”</h3>
                                                <div class="flex pt-4 mt-6 mb-10">
                                                    <img src="assets/images/avatar-2.jpg" class="w-12 h-12 rounded-full" alt="...">
                                                    <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-2">
                                                        <h5 class="text-white font-size-18">Mariya Willam</h5>
                                                        <p class="mb-0 text-white/50">Designer
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <i class="text-5xl text-green-600 bx bxs-quote-alt-left"></i>
                                                <h3 class="mt-4 text-white text-22">“I feel confident imposing change on myself. It's a lot more progressing fun than looking back. That's why I ultricies enim at malesuada nibh diam on tortor neaded to throw curve balls.”</h3>
                                                <div class="flex pt-4 mt-6 mb-10">
                                                    <img src="assets/images/avatar-3.jpg" class="w-12 h-12 rounded-full" alt="...">
                                                    <div class="flex-1 mb-4 ltr:ml-3 rtl:mr-2">
                                                        <h5 class="text-white font-size-18">Jiya Jons</h5>
                                                        <p class="mb-0 text-white/50">Developer
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="{{ asset('/assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/metismenujs/metismenujs.min.js') }}"></script>
        <script src="{{ asset('/assets/libs/simplebar/simplebar.min.js') }}"></script>

        <script src="{{ asset('/assets/libs/swiper/swiper-bundle.min.js') }}"></script>

        <script src="{{ asset('/assets/js/pages/login.init.js') }}"></script>

        <script src="{{ asset('/assets/js/app.js') }}"></script>
    </body>
</html>
