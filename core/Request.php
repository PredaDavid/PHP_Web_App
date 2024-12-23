<?php

namespace core;

class Request
{
    /**
     * Get the path of the request without the GET parameters
     * @return string 
     */
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $pos = strpos($path, '?'); // If we have a get request
        if ($pos === false) {
            return $path;
        }
        return substr($path, 0, $pos);
    }

    /**
     * Get the full URL of the request including the GET parameters
     * @return string The full URL
     */
    public function getUrl()
    {
        $url = $_SERVER['REQUEST_URI'] ?? '/';
        return $url;
    }

    /**
     * Get the content of the request body(POST or GET) and sanitize it for unwanted characters
     * @return array The sanitized data
     */
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

    /**
     * Check if a file was sent in the request
     * @param string $fileInputName The name of the file input field
     * @return bool
     */
    public static function wasFileSent(string $fileInputName)
    {
        if(isset($_FILES[$fileInputName]['name']))
            return !empty($_FILES[$fileInputName]['name']);
        return false;
    }

    /**
     * Get the extension of the file that was sent
     * @param string $fileInputName The name of the file input field
     * @return string|bool The extension of the file or false if no file was sent
     */
    public static function getSendedFileExtension(string $fileInputName)
    {
        if(isset($_FILES[$fileInputName]['name']))
            return strtolower(pathinfo($_FILES[$fileInputName]['name'], PATHINFO_EXTENSION));
        return false;
    }

    /**
     * Save a file to the server
     * @param string $inputFieldName The name of the input field
     * @param string $uploadDirectory The directory where the file will be saved
     * @param string $newFileName The new name of the file. WITHOUT EXTENSION
     * @param bool $canOverwrite If the file can be overwritten
     * @param bool $isImage If the file is an image. Default is true
     * @return array An array of errors
     */
    public static function saveFile($inputFieldName, $uploadDirectory, $newFileName, $canOverwrite = false, $isImage = true)
    {
        $mb = 1048576; // 1MB in bytes
        $errors = [];

        // Undefined | Multiple Files | $_FILES Corruption Attack
        if ( !isset($_FILES[$inputFieldName]['error']) || is_array($_FILES[$inputFieldName]['error'])) {
            $errors[] = 'Invalid parameters.';
        }

        switch ($_FILES[$inputFieldName]['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors[] = 'No file sent.';
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'Exceeded filesize limit of server.';
            default:
                $errors[] = 'Unknown errors.';
        }

        if ($_FILES[$inputFieldName]['size'] > 50 * $mb)
            $errors[] = 'Exceeded filesize limit.';

        $extension = strtolower(pathinfo($_FILES[$inputFieldName]['name'], PATHINFO_EXTENSION));
        if ($isImage and !in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors[] = 'Invalid file format.';
        } else if (!$isImage and !in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'])) {
            $errors[] = 'Invalid file format.';
        }

        // New file name
        $fileName = $uploadDirectory . $newFileName . '.' . $extension;

        // Check if file already exists
        if (!$canOverwrite and file_exists($fileName))
            $errors[] = "File already exists";

        if (!empty($errors)) 
            return $errors;

        if (!move_uploaded_file($_FILES[$inputFieldName]['tmp_name'],$fileName)) {
            $errors[] = "Error while uploading your file.";
            return $errors;
        }

        return []; // empty array means no errors
    }

    /**
     * Check if the request is a POST request
     * @return bool
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if the request is a GET request
     * @return bool
     */
    public function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}
