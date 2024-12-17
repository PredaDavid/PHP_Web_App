<?php

namespace models\sql;

use core\SqlModel;
use DateTime;

class Worker extends SqlModel
{

    const TABLE_NAME = 'worker';

    public int $id;
    public int $user_id;
    public bool $supervisor;
    public bool $can_drive;
    public bool $special_effects_license;
    public bool $status;

    public function __construct()
    {
        parent::__construct();
    }


    public function delete()
    {
        $this->status = 0;
        $this->save();
    }
}
