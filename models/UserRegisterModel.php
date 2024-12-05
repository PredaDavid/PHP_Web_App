<?php

namespace models;

use core\FormModel;
use core\Application;
use core\FormModelField;
use models\UserModel;

class UserRegisterModel extends FormModel
{
    const DB_TABLE = 'user';

    public FormModelField $first_name;
    public FormModelField $last_name;
    public FormModelField $email;
    public FormModelField $phone_number;
    public FormModelField $password;
    public FormModelField $password_confirm;


    public function __construct()
    {
        $this->first_name = new FormModelField(label: "First Name", rules: [self::RULE_REQUIRED]);
        $this->last_name = new FormModelField(FormModelField::TYPE_TEXT, 'Last Name', [self::RULE_REQUIRED]);
        $this->email = new FormModelField(FormModelField::TYPE_EMAIL, 'Email', [self::RULE_REQUIRED, self::RULE_EMAIL, [self::RULE_UNIQUE, 'class' => self::class]]);
        $this->phone_number = new FormModelField(FormModelField::TYPE_TEXT, 'Phone Number', [self::RULE_REQUIRED]);
        $this->password = new FormModelField(FormModelField::TYPE_PASSWORD, 'Password', [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 24]]);
        $this->password_confirm = new FormModelField(FormModelField::TYPE_PASSWORD, 'Password Confirm', [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']]);
    }

    public function register()
    {

        $model = new UserModel();
        $model->loadData([
            'email' => $this->email->value,
            'password' => password_hash($this->password->value, PASSWORD_DEFAULT),
            'first_name' => $this->first_name->value,
            'last_name' => $this->last_name->value,
            'phone_number' => $this->phone_number->value,
            'createdAt' => new \DateTime(),
        ]);
        $model->save(); 
    }
}
