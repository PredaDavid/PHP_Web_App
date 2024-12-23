<?php

namespace core;

/**
 * Class Session
 * The $_SESSION superglobal wrapper
 * For flash messages we have a special array in the session variable called 'flash_messages'
 * This array has 4 keys:
 * error, warning, info, success
 * Each key has an array of messages
 */
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

    public static function setFlashError($message)
    {
        $_SESSION['flash_messages']['error'][] = $message;
    }
    public static function setFlashWarning($message)
    {
        $_SESSION['flash_messages']['warning'][] = $message;
    }
    public static function setFlashInfo($message)
    {
        $_SESSION['flash_messages']['info'][] = $message;
    }
    public static function setFlashSuccess($message)
    {
        $_SESSION['flash_messages']['success'][] = $message;
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