<?php

namespace core;

abstract class FormModel
{
    public const RULE_REQUIRED = '[RULE_REQUIRED] This field is required.'; // Filed is required
    public const RULE_EMAIL = '[RULE_EMAIL] This field must be valid email address.'; // Field must be a valid email address
    public const RULE_MIN = '[RULE_MIN] Min length of this field must be {min}.'; // Min length of the field
    public const RULE_MAX = '[RULE_MAX] Max length of this field must be {max}.'; // Max length of the field
    public const RULE_MATCH = '[RULE_MATCH] This field must be the same as {match}.'; // Field must be the same as another field
    public const RULE_UNIQUE = '[RULE_UNIQUE] Record with this {field} already exists.'; // Field must be unique in the database
    public const RULE_READONLY = '[RULE_READONLY] This field is read only.'; // Field is read only

    protected array $errors = []; 

    public array $fieldsToIgnore = []; 

    public function loadDataFromBody($body) 
    {
        // foreach ($body as $key => $value) {
        //     if (property_exists($this, $key)) {
        //         if ($this->{$key} instanceof FormModelField) {
        //             if ($this->{$key}->type === FormModelField::TYPE_CHECKBOX) {
        //                 $this->{$key}->value = isset($body[$key]);
        //             } else {
        //                 $this->{$key}->value = $value;
        //             }
        //         }
        //     }
        // }
        $properties = get_object_vars($this);
        foreach ($properties as $name => $field) {
            if ($field instanceof FormModelField) {
                if ($field->type === FormModelField::TYPE_CHECKBOX) {
                    $field->value = isset($body[$name]);
                }
                else if (isset($body[$name])) {
                    $field->value = $body[$name];
                }
            }
        }
    }

    public function loadDataFromSqlModel(SqlModel $model) {
        $attributes = get_object_vars($this);
        foreach ($attributes as $name => $attributeValue) {
            if ($attributeValue instanceof FormModelField and isset($model->{$name})) {
                $this->{$name}->value = $model->{$name};
            }
        }
    }

    public function sendDataToSqlModel(SqlModel $model, array $toIgnore=[]) {
        $attributes = get_object_vars($this);
        foreach ($attributes as $name => $field) {
            if (in_array($name, $toIgnore)) continue; // Skip if the field is in the ignore list
            if (!isset($model->{$name})) continue; // Skip if the attribute does not exist in the model
            if ($field->type === FormModelField::TYPE_CHECKBOX) {
                $model->{$name} = $field->value ? 1 : 0;
            }
            else if ($field->type === FormModelField::TYPE_PASSWORD) {
                $model->{$name} = password_hash($field->value, PASSWORD_DEFAULT);
            }
            else {
                $model->{$name} = $field->value;
            }
        }
    }

    public function validate()
    {
        $attributes = get_object_vars($this);
        foreach ($attributes as $name => $attributeValue) {
            if ($attributeValue instanceof FormModelField) {
                $value = $attributeValue->value;
                $rules = $attributeValue->rules;
                foreach ($rules as $rule) {
                    $ruleName = $rule;
                    if (!is_string($ruleName)) {
                        $ruleName = $rule[0];
                    }

                    if ($ruleName === self::RULE_REQUIRED && !$value) {
                        $this->addError($name, self::RULE_REQUIRED);
                    }

                    if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->addError($name, self::RULE_EMAIL);
                    }

                    if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                        $this->addError($name, self::RULE_MIN, $rule);
                    }

                    if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                        $this->addError($name, self::RULE_MAX, $rule);
                    }

                    if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}->value) {
                        $this->addError($name, self::RULE_MATCH, $rule);
                    }
                    if ($ruleName === self::RULE_UNIQUE) {
                        $className = $rule['class'];
                        $uniqueAttr = $name;
                        $tableName = $className::DB_TABLE;
                        $statement = Application::current()->db->pdo->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                        $statement->execute([':attr' => $value]);
                        $record = $statement->fetchObject();
                        if ($record) {
                            $this->addError($name, self::RULE_UNIQUE, ['field' => $name]);
                        }
                    }
                }
            }
        }
        return empty($this->errors);
    }

    public function generateForm()
    {
        $attributes = get_object_vars($this);
        echo "<form action='' method='POST'>";
        foreach ($attributes as $name => $attributeValue) {
            if(in_array($name, $this->fieldsToIgnore)) {
                continue;
            }
            if ($attributeValue instanceof FormModelField) {
                echo $attributeValue;
            }
        }
        echo  "<input type='submit' value='Submit'>";
        echo "</form>";
    }

    public function addError(string $name, string $rule, $params = [])
    {
        $message = $rule;
        if (Config::HIDE_FORM_FIELD_RULE_DEBUG_TEXT) {
            $message = preg_replace('/\[[^\]]*\]/', '', $message);
        }
        foreach ($params as $key => $value) { // replace the placeholder {key} with value
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$name][] = $message;
    }

    public function hasError($name)
    {
        return $this->errors[$name] ?? false;
    }
    public function getFirstError($name)
    {
        return $this->errors[$name][0] ?? false;
    }
}
