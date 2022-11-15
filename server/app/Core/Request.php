<?php

namespace App\Core;

class Request
{
    public function getPath(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        $pos = strpos($uri, '?');

        if (! $pos) return $uri;

        return substr($uri, 0, $pos);
    }

    public function method(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet(): bool
    {
        return $this->method() === 'get';
    }

    public function isPost(): bool
    {
        return $this->method() === 'post';
    }

    public function all(): array
    {
        $input = [];

        if ($this->isGet()) {
            foreach ($_GET as $key => $get) {
                $input[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->isPost()) {
            foreach ($_POST as $key => $post) {
                $input[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return array_merge($input, $_FILES, $_REQUEST);
    }

    public function file($key)
    {
        return $_FILES[$key];
    }

    public function input($key)
    {
        if ($this->isGet()) {
            $key = $_GET[$key];
        }

        if ($this->isPost()) {
            $key = $_POST[$key];
        }

        return $key;
    }

    public function allowCors($allow)
    {
        if ($allow) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 1000');
            }

            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
                }

                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                    header("Access-Control-Allow-Headers: *");
                }

                exit(0);
            }
        }
    }
}