@props([
    'variant' => 'pink', // pink, yellow, dark, purple
    'flip' => false,
])

@php
    $bgClass = match ($variant) {
        'yellow' => 'bg-hf-yellow-400 border-hf-purple-900',
        'dark', 'purple' => 'bg-hf-purple-900 border-hf-purple-900',
        default => 'bg-hf-pink-500 border-hf-purple-900',
    };

    $textColor = match ($variant) {
        'yellow' => 'text-hf-purple-900',
        'dark', 'purple' => 'text-hf-yellow-400',
        default => 'text-white',
    };

    $flipClass = $flip ? 'rotate-180' : '';
@endphp

<div {{ $attributes->merge(['class' => "w-full overflow-hidden border-y-4 border-hf-purple-900 $bgClass $flipClass shadow-hard-sm"]) }}>
    <div class="h-8 md:h-10 w-full flex items-center justify-between px-4 sm:px-8 bg-hf-pattern" style="background-size: 40px 40px;">
        <div class="flex w-full justify-between items-center {{ $textColor }} font-display text-xs sm:text-sm tracking-widest uppercase select-none">
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                HARFEST 2026
            </span>
            <span class="hidden md:inline-flex items-center gap-1.5">
                ✦ HARKAT NEGERI FEST ✦
            </span>
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                LIVE MUSIC FESTIVAL
            </span>
            <span class="hidden lg:inline-flex items-center gap-1.5">
                ★ GET YOUR TICKETS NOW ★
            </span>
        </div>
    </div>
</div>
