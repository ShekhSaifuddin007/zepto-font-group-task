<?php

namespace App\Core;

class Application
{
    /**
     * @var Router
     */
    public Router $router;

    /**
     * @var Request
     */
    public Request $request;

    public function __construct()
    {
        $this->request = new Request;

        $this->router = new Router;
    }

    public function cors($allow = true)
    {
        $this->request->allowCors($allow);
    }

    public function run()
    {
        echo $this->router->resolve();
    }
}