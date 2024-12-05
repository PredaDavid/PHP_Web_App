<?php

namespace core;

// Base class for all controllers
abstract class Controller
{
    public static function render(string $view, array $params = [])
    {
        return Response::renderView($view, $params);
    }

}