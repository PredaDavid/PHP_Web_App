<?php

namespace core;

use models\UserModel;

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
    public Session $session;

    public $user;

    public string $rootPath;

    private function __construct()
    {
        $this->request = new Request();
        $this->router = new Router($this->request);
        $this->session = new Session();
        $this->db = new Database();

        $this->rootPath = str_replace("\\core", "", __DIR__); // Get the root of the project
    }

    public function run()
    {
        if($this->session->getUser()){
            $id = $this->session->getUser();
            $this->user = new UserModel();
            $this->user->loadDataFromDb($id);
        }

        $this->router->resolve();
    }

    public static function isLoggedIn()
    {
        return isset(static::$app->user);
    }
}