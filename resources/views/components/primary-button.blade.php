<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-hf-yellow-400 border-3 border-hf-purple-900 rounded-2xl font-display text-base uppercase tracking-wider text-hf-purple-900 shadow-hard btn-press hover:bg-hf-yellow-500 active:bg-hf-yellow-400 focus:outline-none focus:ring-4 focus:ring-hf-yellow-400/50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
