<nav class="fixed top-0 left-0 right-0 z-10 flex items-center bg-white  dark:bg-zinc-800 print:hidden dark:border-zinc-700">

            <div class="flex justify-between w-full">
                <div class="flex items-center topbar-brand">
                    <div class="hidden lg:flex navbar-brand items-center justify-between shrink px-6 h-[70px]  ltr:border-r rtl:border-l bg-[#fbfaff] border-gray-50 dark:border-zinc-700 dark:bg-zinc-800 shadow-none">
                        <a href="#" class="flex items-center text-lg flex-shrink-0 font-bold dark:text-white leading-[69px]">
                            <img src="{{ asset('/logo_sb.svg') }}" alt="" class="inline-block w-6 h-6 align-middle ltr:xl:mr-2 rtl:xl:ml-2">
                            <span class="hidden font-bold text-gray-700 align-middle xl:block dark:text-gray-100 leading-[69px]">Sarana Bahagia</span>
                        </a>
                    </div>
                    <button type="button" class="group-data-[sidebar-size=sm]:border-b border-b border-[#e9e9ef] dark:border-zinc-600 dark:lg:border-transparent lg:border-transparent  group-data-[sidebar-size=sm]:border-[#e9e9ef] group-data-[sidebar-size=sm]:dark:border-zinc-600 text-gray-800 dark:text-white h-[70px] px-4 ltr:-ml-[52px] rtl:-mr-14 py-1 vertical-menu-btn text-16" id="vertical-menu-btn">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>

                </div>
                <div class="flex justify-between w-full items-center border-b border-[#e9e9ef] dark:border-zinc-600 ltr:pl-6 rtl:pr-6">
                    <div>

                    </div>
                    <div class="flex">
                        <div>
                            <button type="button" class="light-dark-mode text-xl px-3 h-[70px] text-gray-600 dark:text-gray-100 hidden sm:block ">
                                <i data-feather="moon" class="block w-5 h-5 dark:hidden"></i>
                                <i data-feather="sun" class="hidden w-5 h-5 dark:block"></i>
                            </button>
                        </div>
                        <div>
                            <div class="relative dropdown">
                                <button type="button" class="flex items-center px-3 py-2 h-[70px] border-x border-gray-50 bg-gray-50/30  dropdown-toggle dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <img class="border-[3px] border-gray-700 dark:border-zinc-400 rounded-full w-9 h-9 ltr:xl:mr-2 rtl:xl:ml-2" src="{{ asset('/assets/img/profile.jpg') }}" alt="Header Avatar">
                                    <span class="hidden font-medium xl:block text-gray-600 dark:text-gray-100">{{ auth()->user()->name }}</span>
                                    <i class="hidden align-bottom mdi mdi-chevron-down xl:block"></i>
                                </button>
                                <div class="absolute top-0 z-50 hidden w-40 list-none bg-white dropdown-menu dropdown-animation rounded shadow  dark:bg-zinc-800" id="profile/log">
                                    <div class="border border-gray-50 dark:border-zinc-600" aria-labelledby="navNotifications">
                                        <div class="dropdown-item dark:text-gray-100">
                                            <a class="block px-3 py-2 hover:bg-gray-50/50 dark:hover:bg-zinc-700/50" href="{{ 'profile' }}">
                                                <i class="mr-1 align-middle mdi mdi-face-man text-16"></i> Profile
                                            </a>
                                        </div>
                                        <div class="dropdown-item dark:text-gray-100">
                                            <form action="{{ route('logout') }}" method="post">
                                                @csrf
                                                <button class="block p-3 hover:bg-gray-50/50 dark:hover:bg-zinc-700/50" type="submit"><i class="mr-1 align-middle mdi mdi-logout text-16"></i> Logout</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
