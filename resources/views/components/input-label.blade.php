@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-display text-sm tracking-wide uppercase text-hf-purple-900 mb-1']) }}>
    {{ $value ?? $slot }}
</label>
