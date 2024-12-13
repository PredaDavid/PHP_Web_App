<?php

namespace models;

use core\FormModel;
use core\Application;
use core\FormModelField;
use core\SqlModel;
use models\UserModel;

class UserWorkerFormModel extends FormModel
{
    const DB_TABLE = 'user';
    const FORM_SUBMIT_VALUE = 'Update User';

    public FormModelField $id;
    public FormModelField $email;
    public FormModelField $first_name;
    public FormModelField $last_name;
    public FormModelField $phone_number;
    public FormModelField $admin;
    public FormModelField $status;

    public FormModelField $worker;
    public FormModelField $supervisor;
    public FormModelField $can_drive;
    public FormModelField $special_effects_license;

    public function __construct()
    {
        $this->id = new FormModelField(
            name: "id",
            model: $this,
            type: FormModelField::TYPE_NUMBER,
            label: "ID",
            rules: [self::RULE_READONLY]
        );
        $this->email = new FormModelField(
            name: "email",
            model: $this,
            type: FormModelField::TYPE_EMAIL,
            label: 'Email',
            rules: [self::RULE_READONLY]
        );
        $this->first_name = new FormModelField(
            name: "first_name",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'First Name',
            rules: [self::RULE_REQUIRED]
        );
        $this->last_name = new FormModelField(
            name: "last_name",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'Last Name',
            rules: [self::RULE_REQUIRED]
        );
        $this->phone_number = new FormModelField(
            name: "phone_number",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'Phone Number',
            rules: [self::RULE_REQUIRED]
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
            rules: [self::RULE_READONLY]
        );
        $this->worker = new FormModelField(
            name: "worker",
            model: $this,
            type: FormModelField::TYPE_CHECKBOX,
            label: 'Worker',
            rules: []
        );
        $this->supervisor = new FormModelField(
            name: "supervisor",
            model: $this,
            type: FormModelField::TYPE_CHECKBOX,
            label: 'Supervisor',
            rules: []
        );
        $this->can_drive = new FormModelField(
            name: "can_drive",
            model: $this,
            type: FormModelField::TYPE_CHECKBOX,
            label: 'Can Drive',
            rules: []
        );
        $this->special_effects_license = new FormModelField(
            name: "special_effects_license",
            model: $this,
            type: FormModelField::TYPE_CHECKBOX,
            label: 'Special Effects License',
            rules: []
        );

        if(!Application::isAdmin()) {
            $this->admin->rules[] = self::RULE_READONLY;
            $this->worker->rules[] = self::RULE_READONLY;
            $this->supervisor->rules[] = self::RULE_READONLY;
            $this->can_drive->rules[] = self::RULE_READONLY;
            $this->special_effects_license->rules[] = self::RULE_READONLY;
        }
    }

    public function loadDataFromSqlModel(SqlModel $model)
    {
        parent::loadDataFromSqlModel($model);

        if ($model instanceof UserModel) {
            $this->worker->value = $model->worker;
            if ($model->worker) {
                $this->supervisor->value = $model->worker->supervisor;
                $this->can_drive->value = $model->worker->can_drive;
                $this->special_effects_license->value = $model->worker->special_effects_license;
            } else {
                $this->fieldsToIgnore = ['supervisor', 'can_drive', 'special_effects_license'];
            }
        }
    }

    public function sendDataToSqlModel(SqlModel $model, array $toIgnore = ['worker'])
    {

        if ($model instanceof UserModel) {
            if ($this->worker->value and !$model->worker) { // if we set the user as a worker but they are not a worker yet
                $model->worker = new WorkerModel();
                // We try to see if the user was previously a worker
                $wasWorker = WorkerModel::getByWhere('user_id = ?', [$model->id]);
                if(count($wasWorker) !== 0) { // If the user was previously a worker
                    $model->worker = $wasWorker[0];
                    $model->worker->status = 1;
                }
                else { // If the user was not previously a worker
                    $model->worker->status = 1;
                    $model->worker->user_id = $model->id;
                    $model->worker->supervisor = $this->supervisor->value;
                    $model->worker->can_drive = $this->can_drive->value;
                    $model->worker->special_effects_license = $this->special_effects_license->value;
                }
            }
            else if (!$this->worker->value and $model->worker) { // If we unset the user as a worker but they are a worker
                $model->worker->delete();
                $model->worker = false;
            }
            else if ($this->worker->value and $model->worker) { // If we update the worker attributes
                $model->worker->supervisor = $this->supervisor->value;
                $model->worker->can_drive = $this->can_drive->value;
                $model->worker->special_effects_license = $this->special_effects_license->value;
            }
        }

        parent::sendDataToSqlModel($model, $toIgnore);
    }
}
