@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-3 border-hf-purple-900 focus:border-hf-purple-900 focus:ring-4 focus:ring-hf-yellow-400/60 rounded-2xl shadow-hard-sm px-4 py-2.5 text-hf-purple-900 placeholder:text-gray-400 font-sans font-medium transition-all']) }}>
