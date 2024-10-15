<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="relative z-50 col-span-12 md:col-span-5 lg:col-span-4 xl:col-span-3">
        <div class="w-full p-10 bg-white xl:p-12 dark:bg-zinc-800">
            <div class="flex h-[90vh] flex-col">
                <div class="mx-auto mb-12">
                    <a href="index.html" class="">
                        <img src="{{ asset('/logo_sb.svg') }}" alt="" class="inline h-7"> <span class="text-xl font-medium align-middle ltr:ml-1.5 rtl:mr-1.5 dark:text-white">Sarana Bahagia</span>
                    </a>
                </div>

                <div class="my-auto">
                    <div class="text-center">
                        <h5 class="font-medium text-gray-700 dark:text-gray-100">Selamat Datang !</h5>
                        <p class="mt-2 mb-4 text-gray-500 dark:text-gray-100/60">Login untuk masuk kedalam sistem.</p>
                    </div>

                    <form class="pt-2" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <x-input-label class="block mb-2 font-medium text-gray-700 dark:text-gray-100" for="email" :value="__('Email')">Email</x-input-label>
                            <x-text-input type="email" class="w-full py-1.5 border-gray-50 rounded placeholder:text-13 bg-gray-50/30 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 dark:placeholder:text-zinc-100/60 focus:ring focus:ring-violet-500/20 focus:border-violet-100 text-13" id="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div class="mb-3">
                            <div class="flex">
                                <div class="flex-grow-1">
                                    <x-input-label class="block mb-2 font-medium text-gray-600 dark:text-gray-100" for="password" :value="__('Password')" />
                                </div>
                                <div class="ml-auto">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-gray-500 dark:text-gray-100">{{ __('Forgot password?') }}</a>
                                    @endif
                                </div>
                            </div>

                            <div class="flex">
                                <input type="password" id="password" class="w-full py-1.5 border-gray-50 rounded ltr:rounded-r-none rtl:rounded-l-none bg-gray-50/30 placeholder:text-13 text-13 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 dark:placeholder:text-zinc-100/60 focus:ring focus:ring-violet-500/20 focus:border-violet-100" name="password" required autocomplete="current-password" placeholder="Enter password" aria-label="password">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mb-6 row">
                            <div class="col">
                                <div>
                                    <input type="checkbox" class="w-4 h-4 mt-1 align-top transition duration-200 bg-white bg-center bg-no-repeat bg-contain border border-gray-300 rounded cursor-pointer checked:bg-blue-600 checked:border-blue-600 focus:outline-none ltr:float-left rtl:float-right ltr:mr-2 rtl:ml-2 focus:ring-offset-0" id="remember_me" name="remember">
                                    <label class="font-medium text-gray-600 align-middle dark:text-gray-100" for="remember_me">
                                        {{ __('Remember me') }}
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="mb-3">
                            <x-primary-button class="w-full py-2 text-white border-transparent shadow-md btn bg-green-500 w-100 waves-effect waves-light shadow-green-200 dark:shadow-zinc-600" show_spinner="true">{{ __('Log in') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</x-guest-layout>
