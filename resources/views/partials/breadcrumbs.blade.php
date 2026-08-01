@php $last = array_key_last($crumbs); @endphp
<nav class="mb-4 flex flex-wrap items-center gap-y-1 text-sm text-[#8a6c56]">
    @foreach ($crumbs as $index => $crumb)
        @if ($index > 0)
            <span class="mx-2.5 text-[#c4a878]">&raquo;</span>
        @endif
        @if (! empty($crumb['url']) && $index !== $last)
            <a href="{{ $crumb['url'] }}" class="hover:text-[#603b22] hover:underline">{{ $crumb['label'] }}</a>
        @else
            <span class="font-medium text-[#603b22]">{{ $crumb['label'] }}</span>
        @endif
    @endforeach
</nav>
