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
        $this->id = new FormModelField(
            name: "id",
            model: $this,
            type: FormModelField::TYPE_NUMBER,
            label: "ID",
            rules: [self::RULE_REQUIRED]
        );
        $this->email = new FormModelField(
            name: "email",
            model: $this,
            type: FormModelField::TYPE_EMAIL,
            label: 'Email',
            rules: []
        );
        $this->first_name = new FormModelField(
            name: "first_name",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'First Name',
            rules: []
        );
        $this->last_name = new FormModelField(
            name: "last_name",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'Last Name',
            rules: []
        );
        $this->phone_number = new FormModelField(
            name: "phone_number",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'Phone Number',
            rules: []
        );
        $this->admin = new FormModelField(
            name: "admin",
            model: $this,
            type: FormModelField::TYPE_CHECKBOX,
            label: 'Admin',
            rules: []
        );
        $this->status = new FormModelField(
            name: "status",
            model: $this,
            type: FormModelField::TYPE_NUMBER,
            label: 'Status',
            rules: []
        );
    }

}
