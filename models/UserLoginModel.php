<?php

namespace models;

use core\FormModel;
use core\Application;
use core\FormModelField;

class UserLoginModel extends FormModel
{
    const DB_TABLE = UserModel::tableName;

    public FormModelField $email;
    public FormModelField $password;


    public function __construct()
    {
        $this->email = new FormModelField(FormModelField::TYPE_EMAIL, 'Email', [self::RULE_REQUIRED, self::RULE_EMAIL]);
        $this->password = new FormModelField(FormModelField::TYPE_PASSWORD, 'Password', [self::RULE_REQUIRED]);
    }

    public function login() // Returns the user id if the login is successful, otherwise false
    {
        $querry = Application::current()->db->pdo->prepare("SELECT * FROM user WHERE email = ?");
        $querry->execute([$this->email->value]);
        $user = $querry->fetchObject();
        if (!$user) {
            $this->addError('email', 'Email does not exists');
            return false;
        }
        else if (!password_verify($this->password->value, $user->password)) {
            $this->addError('password', 'Password is incorrect');
            return false;
        }
        else {
            return $user->id;
        }   
    }
}
