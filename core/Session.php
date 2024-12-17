<?php

namespace core;

class Session
{
    private static array $flash_messages_to_remove = [];

    public static function startSession()
    {
        session_start(); // Start the session

        static::$flash_messages_to_remove = $_SESSION['flash_messages'] ?? []; // Save the flash messages to remove when the session ends
    }

    public static function endSession()
    {
        // Remove the flash messages
        foreach (static::$flash_messages_to_remove as $key => $value) {
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

    public static function getAllFlashMessages()
    {
        return $_SESSION['flash_messages'] ?? [];
    }

    public static function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }   

}