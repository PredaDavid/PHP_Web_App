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
    public const TYPE_FILE = 'file';

    public $value;
    public string $name;
    public FormModel $model; // The model to which this field belongs
    public string $type;
    public string $label;
    public array $rules = []; // Validation rules
    public array $options = []; // Trasform this filed in a data list

    public function __construct($name, $model, $type = self::TYPE_TEXT, $label = "", $rules = [])
    {
        $this->name = $name;
        $this->model = $model;
        $this->type = $type;
        $this->rules = $rules;

        if ($label === "") {
            $this->label = $name;
        } else {
            $this->label = $label;
        }
    }

    public function __toString()
    {
        $readonly = '';
        if (in_array(FormModel::RULE_READONLY, $this->rules)) {
            if ($this->type === self::TYPE_CHECKBOX) { // readonly is not supported for checkboxes
                $readonly = 'disabled'; // Use disabled instead
            } else {
                $readonly = 'readonly';
            }
        }

        $label = $this->label;
        $type = $this->type;
        $name = $this->name;
        
        $value = "value='" . $this->value ."'" ;
        if($this->type === self::TYPE_CHECKBOX)
            $value = $this->value ? 'checked' : '';

        $br = "<br>";
        if($this->type === self::TYPE_CHECKBOX)
            $br = "";

        $isInvalid = $this->model->hasError($this->name) ? ' is-invalid' : '';
        $firstError = $this->model->getFirstError($this->name);

        $list = "";
        $has_list = "";
        if(count($this->options)!==0){
            $has_list = "list='".$name."_list'";
            $list = "<datalist id='".$name."_list'>";
            foreach($this->options as $option){
                $list .= "<option value='$option'>";
            }
            $list .= "</datalist>";
        }

        $text = "
            <div class='form_field_container'>
                <label>$label:</label>
                $br
                <input type='$type' name='$name' $value $readonly class='form-control$isInvalid' $has_list autocomplete='off'>
                <div class='form_field_error'>
                    $firstError
                </div>
                $list
            </div>";

        return $text;
    }
}
