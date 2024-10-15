<div
    class="fixed bottom-0 z-10 h-screen ltr:border-r rtl:border-l vertical-menu rtl:right-0 ltr:left-0 top-[70px] bg-slate-50 border-gray-50 print:hidden dark:bg-zinc-800 dark:border-neutral-700">

    <div data-simplebar class="h-full">
        <!--- Sidemenu -->
        <div class="metismenu pb-10 pt-2.5" id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul id="side-menu">
                <li class="px-5 py-3 text-xs font-medium text-gray-500 cursor-default leading-[18px] group-data-[sidebar-size=sm]:hidden block"
                    data-key="t-menu">Menu</li>

                <li>
                    <a href="{{ route('dashboard') }}"
                        class="block py-2.5 px-6 text-sm font-medium text-gray-950 transition-all duration-150 ease-linear hover:text-violet-500 dark:text-gray-300 dark:active:text-white dark:hover:text-white">
                        <i class="fa-solid fa-house"></i>
                        <span data-key="t-dashboard"> &nbsp; Dashboard</span>
                    </a>
                </li>

                @foreach ($sub_menu as $menu)
                    <li>
                        <a href="javascript: void(0);" aria-expanded="false" class="block py-2.5 px-6 text-sm font-medium text-gray-950 transition-all duration-150 ease-linear nav-menu hover:text-violet-500 dark:text-gray-300 dark:active:text-white dark:hover:text-white">
                            <i class="fa-solid {{ $menu->first()->menu->icon }}"></i><span data-key="t-pages"> &nbsp; {{ $menu->first()->menu->title }}</span>
                        </a>
                        <ul>
                        @foreach ($menu as $item)
                            <li>
                                <a href="{{ url($item->url) }}" class="pl-[52.8px] pr-6 py-[6.4px] block text-[13.5px] font-medium text-gray-950 transition-all duration-150 ease-linear hover:text-violet-500 dark:text-gray-300 dark:active:text-white dark:hover:text-white">{{ $item->title }}</a>
                            </li>
                        @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
