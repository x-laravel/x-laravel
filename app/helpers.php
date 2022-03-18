<?php

if (!function_exists('cdn')) {
    /**
     * cdn helper
     */
    function cdn(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return \Illuminate\Support\Facades\Storage::disk(config('filesystems.cdn_disk'));
    }
}
