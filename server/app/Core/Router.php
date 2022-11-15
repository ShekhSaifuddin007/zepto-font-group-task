<?php

namespace App\Core;

class Router
{
    protected array $routes = [];

    /**
     * @param string $path
     * @param callable|string $callback
     * @return void
     */
    public function get(string $path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    /**
     * @param string $path
     * @param callable|string $callback
     * @return void
     */
    public function post(string $path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = request()->getPath();

        $method = request()->method();

        $callback = $this->routes[$method][$path] ?? false;

        if (! $callback) {
            abort(404);

            return "Not Found";
        }

        if (is_string($callback)) return $this->render($callback);

        return call_user_func(
            $this->check($callback),
            request()
        );
    }

    public function render($view, $data = [])
    {
        $layout = $this->layout();

        $view = $this->viewContent($view, $data);

        return str_replace("@content", $view, $layout);
    }

    private function layout()
    {
        ob_start();

        require_once rootPath()."/views/layouts/app.php";

        return ob_get_clean();
    }

    private function viewContent($view, $data)
    {
        extract($data, EXTR_SKIP);

        ob_start();

        require_once rootPath()."/views/{$view}.php";

        return ob_get_clean();
    }

    private function check($value)
    {
        if(is_array($value)) {
            $value[0] = new $value[0]();
        }

        return $value;
    }
}