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

    protected array $errors = []; // All the errors are stored here

    public function loadData($data) // Load data from the form
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if($this->{$key} instanceof FormModelField) {
                    $this->{$key}->value = $value;
                }
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
        if(Config::HIDE_FORM_FIELD_RULE_DEBUG_TEXT) {
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


