<?php

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

if (! function_exists('format_wib')) {
    /**
     * Format a date into Indonesian locale with WIB suffix.
     * Default format: 'd F Y, H:i' -> e.g. "28 November 2026, 14:00 WIB"
     */
    function format_wib(mixed $date, string $format = 'd F Y, H:i'): string
    {
        if (blank($date)) {
            return '-';
        }

        if (! $date instanceof CarbonInterface) {
            $date = Carbon::parse($date);
        }

        return $date->locale('id')->translatedFormat($format) . ' WIB';
    }
}

