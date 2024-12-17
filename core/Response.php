<?php

namespace core;

// Render the views and send the response to the browser
abstract class Response
{
    /**
     * Render a view with optional parameters
     * @param string $view The view to render
     * @param array $params The parameters to pass to the view
     * @return void
     */
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

    /**
     * Render simple HTML content with optional layout
     * @param string $content The HTML content to render
     * @param string|bool $layout The layout to use or false if no layout
     * @return void
     */
    public static function renderHtml(string $content, $layout = false)
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

    /**
     * Set the status code of the response
     * @param int $code The status code
     * @return void
     */
    public static function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    /**
     * Redirect to a specific URL
     * @param string $url The URL to redirect to
     * @return void
     */
    public static function redirect(string $url)
    {
        header("Location: $url");
    }

    /**
     * Reload the current page by redirecting to the same URL
     * @return void
     */
    public static function reload()
    {
        header("Location: " . $_SERVER['REQUEST_URI']);
    }

    /**
     * Sends an error response with the specified error message and HTTP status code.
     * @param string $error The title of the error to display.
     * @param string $message The detailed error message to display. Default is an empty string.
     * @param int $code The HTTP status code to set for the response. Default is 500.
     * @return void and stops the script execution
     */
    public static function error(string $error, string $message = "", int $code = 500)
    {
        static::setStatusCode($code);
        $content = "<h1>$error</h1><p>$message</p>";
        static::renderHtml($content, 'layouts/main.php');
        die();
    }
}
