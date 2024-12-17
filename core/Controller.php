<?php

namespace core;

use core\Application;

// Base class for all controllers
abstract class Controller
{
    /**
     * Render a view with optional parameters. 
     * This method is a wrapper around the Response::renderView method
     * @param string $view The view to render
     * @param array $params The parameters to pass to the view
     * @return void
     */
    public static function render(string $view, array $params = [])
    {
        Response::renderView($view, $params);
    }


    /**
     * Get the currently logged-in user.
     * @return User|bool The logged-in user object if a user is logged in, or false if no user is logged in.
     */
    public static function getLoggedInUser()
    {
        if (isset(Application::current()->user))
            return Application::current()->user;
        return false;
    }

    public static function isUserLoggedIn()
    {
        return isset(Application::current()->user);
    }

    public static function isUserAdmin()
    {
        if (!static::isUserLoggedIn()) {
            return false;
        }
        return Application::current()->user->admin;
    }

    public static function isUserWorker()
    {
        if (!static::isUserLoggedIn())
            return false;
        if (Application::current()->user->worker === false)
            return false;
        return true;
    }

    public static function isUserSupervisor()
    {
        if (!static::isUserLoggedIn()) {
            return false;
        }
        if (!static::isUserWorker()) {
            return false;
        }

        return Application::current()->user->worker->supervisor;
    }

    public static function isUserAdminOrSupervisor()
    {
        return static::isUserAdmin() || static::isUserSupervisor();
    }
}
