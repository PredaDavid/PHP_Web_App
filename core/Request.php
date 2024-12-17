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
     * Save an image to the server
     * TODO: Finish this method
     * !!IS NOT FINISHED!!
     * @return string
     */
    public function saveImage()
    {
        try {
            if ( // Undefined | Multiple Files | $_FILES Corruption Attack
                !isset($_FILES['upfile']['error']) ||
                is_array($_FILES['upfile']['error'])
            ) {
                throw new \RuntimeException('Invalid parameters.');
            }

            switch ($_FILES['upfile']['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new \RuntimeException('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new \RuntimeException('Exceeded filesize limit.');
                default:
                    throw new \RuntimeException('Unknown errors.');
            }


            if ($_FILES['upfile']['size'] > 1000000)
                throw new \RuntimeException('Exceeded filesize limit.');

            // $_FILES['upfile']['mime'] should not be trusted;
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            if (false === $ext = array_search(
                $finfo->file($_FILES['upfile']['tmp_name']),
                array(
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                    'jpeg' => 'image/jpeg',
                    'webp' => 'image/webp',
                    'bmp' => 'image/bmp',
                    'gif' => 'image/gif',
                ),
                true
            )) {
                throw new \RuntimeException('Invalid file format.');
            }

            // You should name it uniquely.
            // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
            // On this example, obtain safe unique name from its binary data.
            if (!move_uploaded_file(
                $_FILES['upfile']['tmp_name'],
                sprintf(
                    './uploads/%s.%s',
                    sha1_file($_FILES['upfile']['tmp_name']),
                    $ext
                )
            )) {
                throw new \RuntimeException('Failed to move uploaded file.');
            }

            echo 'File is uploaded successfully.';
        } catch (\RuntimeException $e) {

            echo $e->getMessage();
        }
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
