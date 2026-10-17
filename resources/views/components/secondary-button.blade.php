<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 border-3 border-hf-purple-900 rounded-2xl font-display text-sm uppercase tracking-wider text-hf-purple-900 shadow-hard-sm btn-press hover:bg-gray-200 active:bg-gray-300 focus:outline-none focus:ring-4 focus:ring-purple-300 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
