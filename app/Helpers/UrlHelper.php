<?php

namespace App\Helpers;

class UrlHelper
{
    public static function to(string $path = ''): string
    {
        return url($path);
    }

    public static function asset(string $path = ''): string
    {
        return asset($path);
    }
}
