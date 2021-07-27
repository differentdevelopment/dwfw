<?php

if (!function_exists('log_db')) {
    function log_db()
    {
        \Illuminate\Support\Facades\DB::listen(function ($query) {
            logger($query->sql, $query->bindings);
        });
    }
}


if (!function_exists('remember_with_lock')) {
    function remember_with_lock($key, $callback, $lock_time = 10, $ttl = null)
    {
        return \App\Helpers\Facades\CacheWithLock::remember($key, $callback, $lock_time, $ttl);
    }
}

if (!function_exists('dd500')) {
    function dd500(...$vars)
    {
        foreach ($vars as $v) {
            http_response_code(500);
            \Symfony\Component\VarDumper\VarDumper::dump($v);
        }
        exit(1);
    }
}

if (!function_exists('english_alphabet_only')) {
    function english_alphabet_only(string $string)
    {
        return str_replace(['ű', 'ü', 'ú', 'ó', 'á', 'é', 'í', 'ö', 'ő'], ['u', 'u', 'u', 'o', 'a', 'e', 'i', 'o', 'o'], $string);
    }
}

if (!function_exists('dump500')) {
    function dump500($var, ...$moreVars)
    {
        http_response_code(500);
        \Symfony\Component\VarDumper\VarDumper::dump($var);

        foreach ($moreVars as $v) {
            http_response_code(500);
            \Symfony\Component\VarDumper\VarDumper::dump($v);
        }

        if (1 < func_num_args()) {
            return func_get_args();
        }

        return $var;
    }
}

if (!function_exists('user_can')) {
    function user_can($permission)
    {
        return Auth()->user()->can($permission);
    }
}

if (!function_exists('user_superadmin')) {
    function user_superadmin()
    {
        return Auth()->user()->hasRole('super admin');
    }
}

if (!function_exists('abort_unless_can')) {
    function abort_unless_can($permission, $http_status_code = 403)
    {
        abort_unless(user_can($permission), $http_status_code);
    }
}

if (!function_exists('abort_unless_super_admin')) {
    function abort_unless_super_admin($http_status_code = 403)
    {
        abort_unless(user_superadmin(), $http_status_code);
    }
}
