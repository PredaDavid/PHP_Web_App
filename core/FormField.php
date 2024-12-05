<?php

namespace core;

use core\FormModel;

class FormField 
{
    public FormModel $model;
    public string $attributeName;
    public string $type;

    public function __construct(FormModel $model, string $attributeName)
    {
        $this->model = $model;
        $this->type = $model->{$attributeName}->type;
        $this->attributeName = $attributeName;
    }

    public function __toString()
    {
        return sprintf('
            <div class="form_field_container">
                <label>%s:</label>
                <input type="%s" name="%s" value="%s" class="form-control%s">
                <div class="form_field_error">
                    %s
                </div>
            </div>
        ',
        $this->model->{$this->attributeName}->label ?? $this->attributeName,
        $this->type,
        $this->attributeName,
        $this->model->{$this->attributeName}->value,
        $this->model->hasError($this->attributeName) ? ' is-invalid' : '',
        $this->model->getFirstError($this->attributeName),
        );
    }

}