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

if (!function_exists('getCtrlToErrorType')) {
    function getCtrlToErrorType(string $controller): string
    {
        return str_replace(['App\Http\Controllers\\', '\IndexCTRL', 'CTRL'], null, $controller);
    }
}

if (!function_exists('errorDescriptionTranslate')) {
    function errorDescriptionTranslate(string $type, int $code, string $description): string
    {
        $errorKey = 'errors.' . str_replace('\\', '.', $type) . '.' . $code;
        $translateDescription = __($errorKey);
        return $translateDescription !== $errorKey ? $translateDescription : $description;
    }
}
