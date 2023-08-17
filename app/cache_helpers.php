<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

if (!function_exists('forget_cache')) {
    function forget_cache($key)
    {
        Cache::forget($key);
    }
}

if (!function_exists('to_cache_name')) {
    function to_cache_name($param)
    {
        $cache_name = (string) Str::of(json_encode($param))->replace(':', '_')->slug()->camel()->snake();
        $cache_name = preg_replace(
            [
                "/t[0-9]{8,10}z/",
            ],
            [
                "",
            ],
            $cache_name
        );

        // Log::build([
        //     'driver' => 'single',
        //     'path' => storage_path('logs/caches.log'),
        // ])->info(url()->full());

        // Log::build([
        //     'driver' => 'single',
        //     'path' => storage_path('logs/caches.log'),
        // ])->info(request()->ip . " : $cache_name");

        return $cache_name;
    }
}
