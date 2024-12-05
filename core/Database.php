<?php

namespace core;

use PDO;

class Database
{
    public PDO $pdo;

    public function __construct()
    {
        $database_type = Config::DB_TYPE;
        $host = Config::DB_HOST;    
        $port = Config::DB_PORT;
        $database = Config::DB_NAME;
        $user = Config::DB_USER;
        $password = Config::DB_PASSWORD;

        try {
            if($database_type === "mysql"){
                $this->pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $user, $password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            else if($database_type === "sqlite"){
                $this->pdo = new PDO("sqlite:../$database.db");
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            else {
                Response::error("Database error", "Selected database in Config.php is not supported");
                die();
            }
        } catch (\PDOException $e) {
            Response::error("Database error", $e->getMessage());
            die();
        }
    }
}