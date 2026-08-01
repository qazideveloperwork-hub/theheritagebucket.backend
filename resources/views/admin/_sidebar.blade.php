@php
    $navLink = 'flex items-center gap-2.5 rounded-lg px-3 py-2 hover:bg-[#f1e5d5]';
    $navLinkActive = 'bg-[#f1e5d5] font-medium text-[#603b22]';
    $groupSummary = 'flex cursor-pointer list-none items-center justify-between gap-2.5 px-3 py-2 mt-4 font-semibold text-xs uppercase text-[#8a6c56] hover:text-[#603b22] group-open:text-[#523923] group-open:font-bold [&::-webkit-details-marker]:hidden [&::marker]:content-none';
    $groupSummaryLabel = 'flex items-center gap-2.5';
    $chevron = 'h-3 w-3 shrink-0 text-[#8a6c56] transition-transform duration-200 group-open:rotate-90';
    $icon = 'h-5 w-5 shrink-0';

    $icons = [
        'home' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
        'grid' => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z',
        'cart' => 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.994-4.705 2.598-7.184.121-.5-.106-1.043-.632-1.083H5.418M7.5 14.25 5.106 5.272M9.75 18.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm9 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z',
        'adjustments' => 'M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75',
        'stack' => 'M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m-11.142 0L12 12m9.75-2.25L12 12m9.75 2.25L12 21.75l-9.75-7.5L6.429 12m11.142 0L12 21.75',
        'archive' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z',
        'users' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
        'photo' => 'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z',
        'document' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
    ];
@endphp
@php
    $navIcon = function (string $key) use ($icons, $icon) {
        return '<svg class="'.$icon.'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="'.$icons[$key].'" /></svg>';
    };
@endphp
<aside class="w-64 shrink-0 sticky top-[71px] max-h-[calc(100vh-71px)] overflow-y-auto border-r border-[#e6d9c5] bg-[#fffaf5] p-6">
    <nav class="flex flex-col gap-1 text-sm">
        <a href="{{ route('dashboard') }}" class="{{ $navLink }} {{ request()->routeIs('dashboard') ? $navLinkActive : '' }}">{!! $navIcon('home') !!} Dashboard</a>

        <details class="group" name="sidebar-group" @if (request()->routeIs('products.*', 'categories.*')) open @endif>
            <summary class="{{ $groupSummary }}">
                <span class="{{ $groupSummaryLabel }}">{!! $navIcon('grid') !!} Products</span>
                <svg class="{{ $chevron }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" /></svg>
            </summary>
            <div class="mt-1 flex flex-col gap-1 pl-8">
                <a href="{{ route('products.list') }}" class="{{ $navLink }} {{ request()->routeIs('products.list') ? $navLinkActive : '' }}">Product List</a>
                <a href="{{ route('products.create') }}" class="{{ $navLink }} {{ request()->routeIs('products.create') ? $navLinkActive : '' }}">Create Product</a>
                <a href="{{ route('categories.list') }}" class="{{ $navLink }} {{ request()->routeIs('categories.*') ? $navLinkActive : '' }}">Categories</a>
                <a href="{{ route('products.import') }}" class="{{ $navLink }} {{ request()->routeIs('products.import*') ? $navLinkActive : '' }}">Import Products</a>
                <a href="{{ route('products.export') }}" class="{{ $navLink }}">Export Products</a>
            </div>
        </details>

        <details class="group" name="sidebar-group" @if (request()->routeIs('orders.*')) open @endif>
            <summary class="{{ $groupSummary }} mt-4">
                <span class="{{ $groupSummaryLabel }}">{!! $navIcon('cart') !!} Orders</span>
                <svg class="{{ $chevron }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" /></svg>
            </summary>
            <div class="mt-1 flex flex-col gap-1 pl-8">
                <a href="{{ route('orders.list') }}" class="{{ $navLink }} {{ request()->routeIs('orders.list') || request()->routeIs('orders.edit') ? $navLinkActive : '' }}">Order List</a>
                <a href="{{ route('orders.delayed') }}" class="{{ $navLink }} {{ request()->routeIs('orders.delayed') ? $navLinkActive : '' }}">Delayed Orders</a>
                <a href="{{ route('orders.completed') }}" class="{{ $navLink }} {{ request()->routeIs('orders.completed') ? $navLinkActive : '' }}">Completed Orders</a>
            </div>
        </details>

        <details class="group" name="sidebar-group" @if (request()->routeIs('settings.*', 'infos.*', 'colors.*', 'sizes.*')) open @endif>
            <summary class="{{ $groupSummary }}">
                <span class="{{ $groupSummaryLabel }}">{!! $navIcon('adjustments') !!} Settings</span>
                <svg class="{{ $chevron }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" /></svg>
            </summary>
            <div class="mt-1 flex flex-col gap-1 pl-8">
                <a href="{{ route('settings.shipping') }}" class="{{ $navLink }} {{ request()->routeIs('settings.shipping') ? $navLinkActive : '' }}">Shipping</a>
                <a href="{{ route('settings.tax') }}" class="{{ $navLink }} {{ request()->routeIs('settings.tax') ? $navLinkActive : '' }}">Tax</a>
                <a href="{{ route('settings.options') }}" class="{{ $navLink }} {{ request()->routeIs('settings.options') ? $navLinkActive : '' }}">Options</a>
                <a href="{{ route('colors.list') }}" class="{{ $navLink }} {{ request()->routeIs('colors.*') ? $navLinkActive : '' }}">Colors</a>
                <a href="{{ route('sizes.list') }}" class="{{ $navLink }} {{ request()->routeIs('sizes.*') ? $navLinkActive : '' }}">Sizes</a>
                <a href="{{ route('infos.list') }}" class="{{ $navLink }} {{ request()->routeIs('infos.*') ? $navLinkActive : '' }}">Info Library</a>
            </div>
        </details>

        <details class="group" name="sidebar-group" @if (request()->routeIs('blog.*', 'pages.*')) open @endif>
            <summary class="{{ $groupSummary }}">
                <span class="{{ $groupSummaryLabel }}">{!! $navIcon('document') !!} Pages</span>
                <svg class="{{ $chevron }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" /></svg>
            </summary>
            <div class="mt-1 flex flex-col gap-1 pl-8">
                <a href="{{ route('blog.list') }}" class="{{ $navLink }} {{ request()->routeIs('blog.*') ? $navLinkActive : '' }}">Blog</a>
                <a href="{{ route('pages.shipping-policy') }}" class="{{ $navLink }} {{ request()->routeIs('pages.shipping-policy') ? $navLinkActive : '' }}">Shipping Policy</a>
            </div>
        </details>

        <details class="group" name="sidebar-group" @if (request()->routeIs('sliders.*')) open @endif>
            <summary class="{{ $groupSummary }}">
                <span class="{{ $groupSummaryLabel }}">{!! $navIcon('stack') !!} Sliders</span>
                <svg class="{{ $chevron }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" /></svg>
            </summary>
            <div class="mt-1 flex flex-col gap-1 pl-8">
                <a href="{{ route('sliders.home') }}" class="{{ $navLink }} {{ request()->routeIs('sliders.home') ? $navLinkActive : '' }}">Home banner</a>
                <a href="{{ route('sliders.category') }}" class="{{ $navLink }} {{ request()->routeIs('sliders.category') ? $navLinkActive : '' }}">Category Slider</a>
                <a href="{{ route('sliders.new-arrival') }}" class="{{ $navLink }} {{ request()->routeIs('sliders.new-arrival') ? $navLinkActive : '' }}">New Arrival</a>
                <a href="{{ route('sliders.artisan-story') }}" class="{{ $navLink }} {{ request()->routeIs('sliders.artisan-story') ? $navLinkActive : '' }}">Artisan Story</a>
                <a href="{{ route('sliders.favourite-product') }}" class="{{ $navLink }} {{ request()->routeIs('sliders.favourite-product') ? $navLinkActive : '' }}">Favourite Product</a>
            </div>
        </details>

        <details class="group" name="sidebar-group" @if (request()->routeIs('purchase.*', 'suppliers.*')) open @endif>
            <summary class="{{ $groupSummary }}">
                <span class="{{ $groupSummaryLabel }}">{!! $navIcon('archive') !!} Stock</span>
                <svg class="{{ $chevron }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" /></svg>
            </summary>
            <div class="mt-1 flex flex-col gap-1 pl-8">
                <a href="{{ route('purchase.stock') }}" class="{{ $navLink }} {{ request()->routeIs('purchase.stock') ? $navLinkActive : '' }}">Inventory</a>
                <a href="{{ route('suppliers.list') }}" class="{{ $navLink }} {{ request()->routeIs('suppliers.*') ? $navLinkActive : '' }}">Suppliers</a>
            </div>
        </details>

        <details class="group" name="sidebar-group" @if (request()->routeIs('users.*', 'employees.*')) open @endif>
            <summary class="{{ $groupSummary }}">
                <span class="{{ $groupSummaryLabel }}">{!! $navIcon('users') !!} Users & Staff</span>
                <svg class="{{ $chevron }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" /></svg>
            </summary>
            <div class="mt-1 flex flex-col gap-1 pl-8">
                <a href="{{ route('users.index') }}" class="{{ $navLink }} {{ request()->routeIs('users.index') ? $navLinkActive : '' }}">Users</a>
                <a href="{{ route('employees.index') }}" class="{{ $navLink }} {{ request()->routeIs('employees.index') ? $navLinkActive : '' }}">Employees</a>
            </div>
        </details>

        <a href="{{ route('media.index') }}" class="{{ $navLink }} mt-4 {{ request()->routeIs('media.*') ? $navLinkActive : '' }}">{!! $navIcon('photo') !!} Media Library</a>
    </nav>
</aside>
