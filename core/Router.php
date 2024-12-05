<?php

namespace core;


class Router
{
    private Request $request;

    private array $routes = [];
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function resolve()
    {
        // Get the path from the request; In the future we may delete this class and just add the method
        $path = $this->request->getPath();
        $method = $_SERVER['REQUEST_METHOD'];
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) { // If the route is not found
            Response::error('ERROR 404', 'Page not found', 404);
            return;
        }
        if (is_string($callback)) { // If is a string to a view 
            echo Response::renderView($callback); // Show the view
        } else { // If is a function, meaning a controller
            return call_user_func($callback, $this->request); // Call the function
        }
    }

}
