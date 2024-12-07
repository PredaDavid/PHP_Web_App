<?php

namespace models;

use core\SqlModel;
use DateTime;

class WorkerModel extends SqlModel
{

    CONST tableName = 'worker';

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

        



}
