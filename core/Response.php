<?php

namespace core;

// Render the views and send the response to the browser
abstract class Response
{
    public static function renderView($view, $params = [])
    {
        foreach ($params as $key => $value) {
            $$key = $value; // Create a variable with the key name and assign the value to it
        }

        ob_start(); // Start output buffering; output will be keeped not sent to the browser
        include_once Application::current()->rootPath . "/views/$view.php";
        $content = ob_get_clean(); // Return the content of the output buffer and clears it

        if (defined('USE_LAYOUT')) { // If in the view we have defined a layout
            ob_start(); // Start the buffer again
            $layout = USE_LAYOUT; // Get the layout
            include_once Application::current()->rootPath . "/views/$layout"; // Include the layout
            ob_flush(); // Send the output buffer to the browser
        } else { // If we don't have a layout. Just print the content
            echo $content;
        }
    }

    public static function renderHtml($content, $layout = false)
    {
        if (!$layout) {
            echo $content;
            return;
        }
        ob_start(); // Start the buffer
        include_once Application::current()->rootPath . "/views/$layout"; // Include the layout
        ob_flush(); // Send the output buffer to the browser
        return;
    }

    public static function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public static function redirect(string $url)
    {
        header("Location: $url");
    }

    public static function reload()
    {
        header("Location: " . $_SERVER['REQUEST_URI']);
    }

    public static function error(string $error, string $message = "", int $code = 500)
    {
        static::setStatusCode($code);
        $content = "<h1>$error</h1><p>$message</p>";
        static::renderHtml($content, 'layouts/main.php');
        die();
    }
}
