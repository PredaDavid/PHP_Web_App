<?php

namespace models;

use core\SqlModel;
use DateTime;

class UserModel extends SqlModel
{

    CONST TABLE_NAME = 'user';
    CONST EXTRA_ATTRIBUTES = ['worker'];


    // Columns in table
    public int $id;
    public string $email;
    public string $password;
    public string $first_name;
    public string $last_name;
    public string $phone_number;
    public bool $admin;
    public int $status;
    public DateTime $created_at;

    // Other useful attributes
    public $worker;


    public function __construct()
    {
        parent::__construct();
    }


    public function loadDataFromDb($_id = "")
    {
        parent::loadDataFromDb($_id);
        $this->worker = WorkerModel::getByWhere('user_id=?', [$this->id]);
        if (sizeof($this->worker) > 0) {
            $this->worker = $this->worker[0];
        }
        else {
            $this->worker = false;
        }
    }

    public function save() 
    {
        // var_dump($this->worker);
        // die();
        if ($this->worker instanceof WorkerModel) {
            $this->worker->save();
        }
        parent::save();
    }


}
