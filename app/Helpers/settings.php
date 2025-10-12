<?php

use App\Models\Setting;

if (! function_exists('get_setting')) {
    function get_setting($key, $default = null)
    {
        return optional(Setting::where('key', $key)->first())->value ?? $default;
    }
}
