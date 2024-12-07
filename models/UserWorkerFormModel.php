<?php

namespace models;

use core\FormModel;
use core\Application;
use core\FormModelField;
use models\UserModel;

class UserWorkerFormModel extends FormModel
{
    const DB_TABLE = 'user';

    public FormModelField $id;
    public FormModelField $email;
    public FormModelField $first_name;
    public FormModelField $last_name;
    public FormModelField $phone_number;
    public FormModelField $admin;
    public FormModelField $status;


    public function __construct()
    {
        $this->id = new FormModelField(label: "ID", rules: [self::RULE_READONLY]);
        $this->email = new FormModelField(FormModelField::TYPE_EMAIL, 'Email', [self::RULE_READONLY]);
        $this->first_name = new FormModelField(label: "First Name");
        $this->last_name = new FormModelField(FormModelField::TYPE_TEXT, 'Last Name');
        $this->phone_number = new FormModelField(FormModelField::TYPE_TEXT, 'Phone Number');
        $this->admin = new FormModelField(FormModelField::TYPE_CHECKBOX, 'Admin');
        $this->status = new FormModelField(FormModelField::TYPE_CHECKBOX, 'Status');
    }

}
