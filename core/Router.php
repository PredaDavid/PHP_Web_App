<?php

namespace core;


class Router
{
    private Request $request;

    private array $routes = [];

    public function __construct(Request $request)
    {
        $this->request = $request; // Save the request object
    }

    /**
     * Add a GET route to the router
     * @param string $path The path of the route
     * @param callback|string $callback The callback of the route (function or string to a view)
     * @return void
     */
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    /**
     * Add a POST route to the router
     * @param string $path The path of the route
     * @param callback|string $callback The callback of the route (function or string to a view)
     * @return void
     */
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    /**
     * Resolve the routes and call the callback
     * @return void
     */
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $_SERVER['REQUEST_METHOD'];
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) { // If the route is not found
            Response::error('ERROR 404', 'Page not found', 404);
        }
        if (is_string($callback)) { // If is a string to a view 
            Response::renderView($callback); 
        } else { // If is a function
            call_user_func($callback, $this->request); // Call the function
        }
    }

}
