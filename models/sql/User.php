<?php

namespace models\sql;

use core\SqlModel;
use DateTime;

class User extends SqlModel
{

    const TABLE_NAME = 'user';
    const EXTRA_ATTRIBUTES = ['worker'];

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
    public $worker; // Worker or false


    public function __construct()
    {
        parent::__construct();
    }

    public function loadDataFromDb($_id = "")
    {
        parent::loadDataFromDb($_id);
        $this->worker = Worker::getByWhere('user_id=? and status=?', [$this->id, 1]);
        if (sizeof($this->worker) > 0) {
            $this->worker = $this->worker[0];
        } else {
            $this->worker = false;
        }
    }

    public function save()
    {
        if ($this->worker instanceof Worker) {
            $this->worker->save();
        }
        parent::save();
    }

    public function delete()
    {
        if ($this->worker instanceof Worker) {
            $this->worker->delete();
        }
        $this->status = 0;
        $this->save();
    }
}
