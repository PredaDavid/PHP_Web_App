<?php

namespace models\form;

use core\FormModel;
use core\Application;
use core\FormModelField;

use models\sql\User;

class UserRegisterForm extends FormModel
{
    const DB_TABLE = 'user';
    const FORM_SUBMIT_VALUE = 'Register';

    public FormModelField $first_name;
    public FormModelField $last_name;
    public FormModelField $email;
    public FormModelField $phone_number;
    public FormModelField $password;
    public FormModelField $password_confirm;

    public function __construct()
    {
        $this->first_name = new FormModelField(
            name: "first_name",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: "First Name",
            rules: [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 100]]
        );
        $this->last_name = new FormModelField(
            name: "last_name",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'Last Name',
            rules: [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 100]]
        );
        $this->email = new FormModelField(
            name: "email",
            model: $this,
            type: FormModelField::TYPE_EMAIL,
            label: 'Email',
            rules: [
                self::RULE_REQUIRED,
                self::RULE_EMAIL,
                [self::RULE_MAX, 'max' => 200],
                [self::RULE_UNIQUE, 'table' => 'user', 'column' => 'email']
            ]
        );
        $this->phone_number = new FormModelField(
            name: "phone_number",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'Phone Number',
            rules: [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 20]]
        );
        $this->password = new FormModelField(
            name: "password",
            model: $this,
            type: FormModelField::TYPE_PASSWORD,
            label: 'Password',
            rules: [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 24]]
        );
        $this->password_confirm = new FormModelField(
            name: "password_confirm",
            model: $this,
            type: FormModelField::TYPE_PASSWORD,
            label: 'Password Confirm',
            rules: [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']]
        );
    }

    public function register()
    {

        $model = new User();
        $this->loadDataToSqlModel($model);
        $model->status = 1;
        $model->created_at = Application::current()->currentDateTime;
        $model->save();
    }
}
