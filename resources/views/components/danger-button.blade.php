<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-red-600 border-3 border-hf-purple-900 rounded-2xl font-display text-sm uppercase tracking-wider text-white shadow-hard btn-press hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-400/50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
