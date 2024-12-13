<?php

namespace core;

class Request
{
    // Get the url path
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $pos = strpos($path, '?'); // If we have a get request
        if ($pos === false) {
            return $path;
        }
        return substr($path, 0, $pos);
    }

    public function getUrl()
    {
        $url = $_SERVER['REQUEST_URI'] ?? '/';
        return $url;
    }

    public function getBody()
    {
        $body = [];
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            foreach ($_GET as $key => $value) {
                // Sanitize the data for unwanted characters like <, >, ', " etc.
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }

    public function isPost(){
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function isGet(){
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}