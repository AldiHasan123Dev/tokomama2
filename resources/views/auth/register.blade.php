<x-guest-layout>
    {{-- <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form> --}}

    <div class="relative z-50 col-span-12 md:col-span-5 lg:col-span-4 xl:col-span-3">
        <div class="w-full p-10 bg-white xl:p-12 dark:bg-zinc-800">
            <div class="flex h-[90vh] flex-col">
                <div class="mx-auto mb-12">
                    <a href="index.html" class="">
                        <img src="assets/images/logo-sm.svg" alt="" class="inline h-7"> <span class="text-xl font-medium align-middle ltr:ml-1.5 rtl:mr-1.5 dark:text-white">Minia</span>
                    </a>
                </div>

                <div class="my-auto">
                    <div class="text-center">
                        <h5 class="font-medium text-gray-700 dark:text-gray-100">Register Account</h5>
                        <p class="mt-2 mb-4 text-gray-500 dark:text-gray-100/60">Get your free Minia account now.</p>
                    </div>

                    <form class="pt-2" action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <x-input-label class="block mb-2 font-medium text-gray-700 dark:text-gray-100" for="name" :value="__('Name')">Name</x-input-label>
                            <x-text-input type="text" class="w-full py-1.5 border-gray-50 rounded placeholder:text-13 bg-gray-50/30 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 dark:placeholder:text-zinc-100/60 focus:ring focus:ring-violet-500/20 focus:border-violet-100 text-13" id="name" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div class="mb-3">
                            <x-input-label class="block mb-2 font-medium text-gray-700 dark:text-gray-100" for="email" :value="__('Email')">Email</x-input-label>
                            <x-text-input type="email" class="w-full py-1.5 border-gray-50 rounded placeholder:text-13 bg-gray-50/30 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 dark:placeholder:text-zinc-100/60 focus:ring focus:ring-violet-500/20 focus:border-violet-100 text-13" id="email" name="email" :value="old('email')" required autofocus autocomplete="email" placeholder="Enter email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div class="mb-3">
                            <div class="flex">
                                <div class="flex-grow-1">
                                    <x-input-label class="block mb-2 font-medium text-gray-600 dark:text-gray-100" for="password" :value="__('Password')" />
                                </div>
                            </div>

                            <div class="flex">
                                <x-text-input id="password" class="w-full py-1.5 border-gray-50 rounded ltr:rounded-r-none rtl:rounded-l-none bg-gray-50/30 placeholder:text-13 text-13 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 dark:placeholder:text-zinc-100/60 focus:ring focus:ring-violet-500/20 focus:border-violet-100" type="password" name="password" required autocomplete="new-password" placeholder="Enter password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mb-5">
                            <div class="flex">
                                <div class="flex-grow-1">
                                    <x-input-label class="block mb-2 font-medium text-gray-600 dark:text-gray-100" for="password_confirmation" :value="__('Confirm Password')" />
                                </div>
                            </div>

                            <div class="flex">
                                <x-text-input id="password_confirmation" class="w-full py-1.5 border-gray-50 rounded ltr:rounded-r-none rtl:rounded-l-none bg-gray-50/30 placeholder:text-13 text-13 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 dark:placeholder:text-zinc-100/60 focus:ring focus:ring-violet-500/20 focus:border-violet-100" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Enter password again" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <x-primary-button class="w-full py-2 text-white border-transparent shadow-md btn bg-violet-500 w-100 waves-effect waves-light shadow-violet-200 dark:shadow-zinc-600">{{ __('Register') }}</x-primary-button>
                        </div>
                    </form>

                    <div class="pt-2 mt-5 text-center">
                        <div>
                            <h6 class="mb-3 font-medium text-gray-500 text-14 dark:text-gray-100">- Sign in with -</h6>
                        </div>

                        <div class="flex justify-center gap-3">
                            <a href="" class="w-8 h-8 leading-8 rounded-full bg-violet-500">
                                <i class="text-sm text-white fa-brands fa-facebook"></i>
                            </a>
                            <a href="" class="w-8 h-8 leading-8 rounded-full bg-gray-800">
                                <i class="text-sm text-white fa-brands fa-x-twitter"></i>
                            </a>
                            <a href="" class="w-8 h-8 leading-8 bg-red-500 rounded-full">
                                <i class="text-sm text-white fa-brands fa-google"></i>
                            </a>
                        </div>
                    </div>

                    <div class="mt-12 text-center">
                        <p class="text-gray-500 dark:text-gray-100">Already have an account ? <a href="{{ route('login') }}" class="font-semibold text-violet-500"> Login </a> </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
