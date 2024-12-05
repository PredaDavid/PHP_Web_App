<?php

namespace core;

class Session
{
    private $flash_messages_to_remove = [];

    public function __construct()
    {
        session_start();

        $this->flash_messages_to_remove = $_SESSION['flash_messages'] ?? [];

    }

    public function __destruct()
    {

        foreach ($this->flash_messages_to_remove as $key => $value) {
            unset($_SESSION['flash_messages'][$key]);
        }
    }

    public static function setFlash($key, $message)
    {
        $_SESSION['flash_messages'][$key] = $message;
    }

    public static function getFlash($name)
    {
        if(isset($_SESSION['flash_messages'][$name])) {
            return $_SESSION['flash_messages'][$name];
        }
        return false;
    }

    public static function getUser()
    {
        return $_SESSION['user'] ?? false;
    }

}