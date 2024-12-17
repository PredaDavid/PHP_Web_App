<?php

namespace models\form;

use core\FormModel;
use core\Application;
use core\FormModelField;

use models\sql\User;

class UserLoginForm extends FormModel
{
    const DB_TABLE = User::TABLE_NAME;

    public FormModelField $email;
    public FormModelField $password;


    public function __construct()
    {
        $this->email = new FormModelField(
            name: "email",
            model: $this,
            type: FormModelField::TYPE_EMAIL,
            label: 'Email',
            rules: [self::RULE_REQUIRED, self::RULE_EMAIL]
        );
        $this->password = new FormModelField(
            name: "password",
            model: $this,
            type: FormModelField::TYPE_PASSWORD,
            label: 'Password',
            rules: [self::RULE_REQUIRED]
        );
    }

    public function login() // Returns the user id if the login is successful, otherwise false
    {
        $querry = Application::current()->db->pdo->prepare("SELECT * FROM user WHERE email = ?");
        $querry->execute([$this->email->value]);
        $user = $querry->fetchObject();
        if (!$user) {
            $this->addError('email', 'Email does not exists');
            return false;
        } else if (!password_verify($this->password->value, $user->password)) {
            $this->addError('password', 'Password is incorrect');
            return false;
        } else {
            return $user->id;
        }
    }
}
