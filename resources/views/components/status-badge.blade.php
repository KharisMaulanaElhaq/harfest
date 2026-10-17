<?php

namespace resources\views\components;
?>
@props(['status'])

@php
    $statusValue = is_string($status) ? $status : $status->value;
    $enumStatus = is_string($status) 
        ? (\App\Enums\OrderStatus::tryFrom($status) 
            ?? \App\Enums\PaymentStatus::tryFrom($status) 
            ?? \App\Enums\EventStatus::tryFrom($status)) 
        : $status;

    $label = $enumStatus ? $enumStatus->label() : ucfirst(str_replace('_', ' ', $statusValue));

    $colorClasses = match ($statusValue) {
        // Event statuses
        'on_sale' => 'bg-hf-yellow-400 text-hf-purple-900 border-hf-purple-900',
        'upcoming' => 'bg-hf-blue-400 text-hf-purple-900 border-hf-purple-900',
        'finished' => 'bg-hf-purple-900 text-hf-yellow-400 border-hf-purple-900',
        'draft' => 'bg-gray-400 text-gray-900 border-hf-purple-900',

        // Order & Payment statuses
        'pending_payment' => 'bg-hf-yellow-400 text-hf-purple-900 border-hf-purple-900',
        'waiting_verification', 'pending' => 'bg-hf-blue-400 text-hf-purple-900 border-hf-purple-900',
        'paid', 'approved' => 'bg-emerald-400 text-emerald-950 border-hf-purple-900',
        'rejected' => 'bg-rose-500 text-white border-hf-purple-900',
        'expired', 'cancelled' => 'bg-gray-400 text-gray-900 border-hf-purple-900',
        default => 'bg-gray-200 text-gray-800 border-hf-purple-900',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-3.5 py-1 text-xs md:text-sm font-sans font-black uppercase tracking-wider rounded-xl border-2 shadow-hard-sm $colorClasses"]) }}>
    {{ $label }}
</span>
