<?php

namespace models;

use core\SqlModel;
use DateTime;

class UserModel extends SqlModel
{

    CONST tableName = 'user';

    public int $id;
    public string $email;
    public string $password;
    public string $first_name;
    public string $last_name;
    public string $phone_number;
    public bool $admin;
    public int $status;
    public DateTime $created_at;

    public function __construct()
    {
        parent::__construct();
    }



}
