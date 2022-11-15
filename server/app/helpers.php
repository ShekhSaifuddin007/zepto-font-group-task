<?php

use App\Core\Request;
use App\Core\Router;

if (! function_exists('view')) {
    function view(string $view, $data = []) {
        return (new Router)->render($view, $data);
    }
}

if (! function_exists('abort')) {
    function abort(int $code) {
        return http_response_code($code);
    }
}

if (! function_exists('response')) {
    function response($value, int $code = 200) {
        abort($code);

        return json_encode($value);
    }
}

if (! function_exists('dd')) {
    function dd(...$vars) {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }

        exit(1);
    }
}

if (! function_exists('redirect')) {
    function redirect($location = '/', $base = false)
    {
        if ($base) {
            header('Location: '.$location);
            exit();
        }

        header('Location: '.BASE_URL.'/'.$location);

        exit(1);
    }
}

if (! function_exists('request')) {
    function request(): Request
    {
        return new Request();
    }
}

if (! function_exists('rootPath')) {
    function rootPath($path = null): string
    {
        if (is_null($path)) {
            return dirname(__DIR__);
        }

        return dirname(__DIR__)."/{$path}";
    }
}

if (! function_exists('asset')) {
    function asset($path): string
    {
        return BASE_URL."/{$path}";
    }
}

if (! function_exists('config')) {
    function config($key = null)
    {
        [$file, $value] = explode('.', $key, 2);

        $elements = null;

        if($elements === null) { // check if null to load it from the file
            $elements = require rootPath()."/configuration/{$file}.php";
        }

        return $elements[$value];
    }
}
