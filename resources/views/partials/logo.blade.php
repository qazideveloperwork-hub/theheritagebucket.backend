@php
    $iconSize = $iconSize ?? 24;
    $gap = $gap ?? 8;
    $textClass = $textClass ?? 'text-headline-sm lg:text-headline-md';
@endphp
<span class="inline-flex items-center" style="gap: {{ $gap }}px;">
    <span class="font-serif {{ $textClass }} tracking-tight leading-none">
        <span class="text-primary">Heritage</span> <span class="text-secondary">Basket</span>
    </span>
    <svg width="{{ $iconSize }}" height="{{ $iconSize }}" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0" style="position: relative; top: -6px;">
        {{-- sprig --}}
        <path d="M24,7 C21,5 18,3 16,4 C17,6 20,8.5 24,7 Z" fill="#825100" />
        <path d="M24,7 C27,5 30,3 32,4 C31,6 28,8.5 24,7 Z" fill="#825100" />
        {{-- handle --}}
        <path d="M14,20 Q14,5 24,5 Q34,5 34,20" stroke="#97472d" stroke-width="2.5" stroke-linecap="round" fill="none" />
        {{-- basket body --}}
        <path d="M10,20 L38,20 L32,42 L16,42 Z" fill="#a5360d" fill-opacity="0.12" stroke="#a5360d" stroke-width="1.75" />
        {{-- rim --}}
        <ellipse cx="24" cy="20" rx="14" ry="2.75" fill="#97472d" />
        {{-- weave lines --}}
        <path d="M11.6,26 L36.4,26" stroke="#97472d" stroke-width="1.25" stroke-linecap="round" stroke-opacity="0.75" />
        <path d="M13.3,32 L34.7,32" stroke="#97472d" stroke-width="1.25" stroke-linecap="round" stroke-opacity="0.75" />
        <path d="M14.9,38 L33.1,38" stroke="#97472d" stroke-width="1.25" stroke-linecap="round" stroke-opacity="0.75" />
    </svg>
</span>
