<?php

include_once "core/Config.php";

const HELP = 'help';
const CREATE_DATABASE = 'create_database';
const DELETE_DATABASE = 'delete_database';
const CREATE_ADMIN_USER = 'create_admin_user';

if(!isset($argv[1])){
    die("You need to use a command\n");
}
switch ($argv[1]) {
    case HELP:
        echo "To run a command, type: php manage.php command_name \n";
        echo "Available commands: create_database, create_admin_user, help\n";
        echo "  - create_database: Creates the database, the tables and populates them with some data\n";
        echo "  - create_admin_user: Creates a super user (admin)\n";
        echo "  - help: Shows this message\n";
        echo "  - delete_database: Deletes the database\n";
        break;
    case CREATE_DATABASE:

        $database_type = core\Config::DB_TYPE;
        $host = core\Config::DB_HOST;
        $port = core\Config::DB_PORT;
        $database = core\Config::DB_NAME;
        $user = core\Config::DB_USER;
        $password = core\Config::DB_PASSWORD;
    
        try {
            if ($database_type === "mysql") {
                $pdo = new PDO("mysql:host=$host;port=$port", $user, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } else if ($database_type === "sqlite") {
                $pdo = new PDO("sqlite:$database.db");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } else {
                die("Database not suported");
            }
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    
        $create_user_table = "";
        if ($database_type === "mysql") {
            $create_user_table = file_get_contents("migrations/000_create_database.sql");
        } else if ($database_type === "sqlite") {
            $create_user_table = file_get_contents("migrations/000_create_database.sqlite");
        }
    
        if ($create_user_table === false) {
            die("Error loading SQL file");
        }
    
        try {
            $pdo->exec($create_user_table);
            echo "Database created successfully.";
        } catch (\PDOException $e) {
            die("Failed to create table: " . $e->getMessage());
        }
        break;
    case DELETE_DATABASE:
        $answer = readline("\Which database you want to delete (mysql/sqlite): ");
        $confirm = readline("\nAre you sure you want to delete the database? You will need to run create_database again (yes/no): ");
        if ($confirm !== "yes") {
            die("Operation canceled");
        }
        if ($answer === "mysql") {
            $host = core\Config::DB_HOST;
            $port = core\Config::DB_PORT;
            $database = core\Config::DB_NAME;
            $user = core\Config::DB_USER;
            $password = core\Config::DB_PASSWORD;
            $pdo = new PDO("mysql:host=$host;port=$port", $user, $password);
            $sql = "DROP DATABASE $database";
            $pdo->exec($sql);
            echo "Database deleted successfully.";
        } else if ($answer === "sqlite") {
            $database = core\Config::DB_NAME;
            $pdo = new PDO("sqlite:$database.db");
            $pdo->exec("PRAGMA writable_schema = 1;
                        DELETE FROM sqlite_master;
                        PRAGMA writable_schema = 0;
                        VACUUM;
                        PRAGMA integrity_check;");
            echo "Database deleted successfully.";
        } else {
            die("Database not suported");
        }
        break;
    case CREATE_ADMIN_USER:
        $database_type = core\Config::DB_TYPE;

        $email = readline("Email: ");
        $password = readline("Password: ");
        $password = password_hash($password, PASSWORD_DEFAULT);
        //Check if user with same email allready exists
        if (empty($email) or empty($password)) {
            die("\nEmail and password are required");
        }
        if ($database_type === "mysql") {
            $host = core\Config::DB_HOST;
            $port = core\Config::DB_PORT;
            $database = core\Config::DB_NAME;
            $user = core\Config::DB_USER;
            $psw = core\Config::DB_PASSWORD;
            $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $user, $psw);
        } else if ($database_type === "sqlite") {
            $database = core\Config::DB_NAME;
            $pdo = new PDO("sqlite:$database.db");
        }
    
        $sql = "SELECT * FROM user WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            die("\nUser with this email allready exists");
        }
        // Insert user in database
        $sql = "INSERT INTO user (email,  password, status, first_name, last_name, admin) VALUES (:email, :password, 4, ' ', ' ', 1)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute(['email' => $email, 'password' => $password]);
        } catch (\PDOException $th) {
            die("\nFailed to create superuser: " . $th->getMessage());
        }
        echo "Superuser created successfully.";
        break;
    default:
        echo "Command not found\nUse help to see the available commands\n";
        break;
}