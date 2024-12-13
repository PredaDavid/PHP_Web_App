<?php

namespace core;

use core\FormModel;

class FormModelField
{
    public const TYPE_TEXT = 'text';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';
    public const TYPE_EMAIL = 'email';
    public const TYPE_DATE = 'date';
    public const TYPE_CHECKBOX = 'checkbox';

    public $value;
    public string $type;
    public string $name;
    public string $label;
    public FormModel $model;
    public array $rules = [];


    public function __construct($name,$model,$type = self::TYPE_TEXT,$label = "", $rules = [])
    {
        $this->name = $name;
        $this->model = $model;
        $this->type = $type;
        $this->rules = $rules;

        if($label === "") {
            $this->label = $name;       
        } else {
            $this->label = $label;
        }
    }

    public function __toString()
    {
        $readonly = '';
        if(in_array(FormModel::RULE_READONLY, $this->rules)) {
            if($this->type === self::TYPE_CHECKBOX) {
                $readonly = 'disabled';
            } else {
                $readonly = 'readonly';
            }
        }

        if ($this->type === self::TYPE_CHECKBOX) {
            $value = $this->value ? 'checked' : '';
            $text = sprintf('
                <div class="form_field_container">
                    <label>%s: </label>
                    <input type="%s" name="%s" %s %s class="form-control%s">
                    <div class="form_field_error">
                        %s
                    </div>
                </div>
            ',
            $this->label,
            $this->type,
            $this->name,
            $value,
            $readonly,
            $this->model->hasError($this->name) ? ' is-invalid' : '',
            $this->model->getFirstError($this->name),
            );
        } else {
            $text = sprintf('
                <div class="form_field_container">
                    <label>%s:</label>
                    <br>
                    <input type="%s" name="%s" value="%s" %s class="form-control%s">
                    <div class="form_field_error">
                        %s
                    </div>
                </div>
            ',
            $this->label,
            $this->type,
            $this->name,
            $this->value,
            $readonly,
            $this->model->hasError($this->name) ? ' is-invalid' : '',
            $this->model->getFirstError($this->name),
            );
        }
        return $text;
    }
}
