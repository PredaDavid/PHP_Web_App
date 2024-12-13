<?php

namespace models;

use core\FormModel;
use core\Application;
use core\FormModelField;
use models\UserModel;

class UserChangePasswordModel extends FormModel
{
    const DB_TABLE = 'user';
    const FORM_SUBMIT_VALUE = 'Change Password';

    public FormModelField $old_password;
    public FormModelField $new_password;
    public FormModelField $new_password_confirm;


    public function __construct()
    {
        $this->old_password = new FormModelField(
            name: "old_password",
            model: $this,
            type: FormModelField::TYPE_PASSWORD,
            label: 'Old Password',
            rules: [self::RULE_REQUIRED]
        );
        $this->new_password = new FormModelField(
            name: "new_password",
            model: $this,
            type: FormModelField::TYPE_PASSWORD,
            label: 'New Password',
            rules: [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 24]]
        );
        $this->new_password_confirm = new FormModelField(
            name: "new_password_confirm",
            model: $this,
            type: FormModelField::TYPE_PASSWORD,
            label: 'New Password Confirm',
            rules: [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'new_password']]
        );
    }

    public function changePassword()
    {
        $user = Application::current()->user;
        if (!password_verify($this->old_password->value, $user->password)) {
            $this->addError('old_password', 'Old password is incorrect');
            return false;
        }
        $user->password = password_hash($this->new_password->value, PASSWORD_DEFAULT);
        $user->save();
        return true;
    }
}
