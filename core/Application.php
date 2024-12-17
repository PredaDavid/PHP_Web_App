<?php

namespace core;

use models\sql\User;

// Main class of the framework
class Application
{
    // Singleton pattern; only one instance of the class can be created
    private static Application $app;
    public static function current(): Application
    {
        if (!isset(static::$app)) {
            static::$app = new Application();
        }
        return static::$app;
    }

    public Router $router;
    public Request $request;
    public Database $db;

    public $user; // The logged in user

    public string $rootPath;
    public \DateTime $currentDateTime; // The current date and time; This is used for testing purposes

    private function __construct()
    {
        Session::startSession();

        $this->request = new Request();
        $this->router = new Router($this->request);
        $this->db = new Database();

        $this->rootPath = str_replace("\\core", "", __DIR__); // Get the root of the project
        $this->currentDateTime = new \DateTime('now');
    }

    public function __destruct()
    {
        Session::endSession();
    }

    public function run()
    {
        // Check if the user is logged in and load the user object
        if (Session::get('user_id')) {
            $id = Session::get('user_id');
            $this->user = new User();
            $this->user->loadDataFromDb($id);
        }

        $this->router->resolve();
    }
}
