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
        foreach ($attributes as $attributeName => $attributeValue) {
            if ($attributeValue instanceof FormModelField) {
                $value = $attributeValue->value;
                $rules = $attributeValue->rules;
                foreach ($rules as $rule) {
                    $ruleName = $rule;
                    if (!is_string($ruleName)) {
                        $ruleName = $rule[0];
                    }

                    if ($ruleName === self::RULE_REQUIRED && !$value) {
                        $this->addError($attributeName, self::RULE_REQUIRED);
                    }

                    if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->addError($attributeName, self::RULE_EMAIL);
                    }

                    if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                        $this->addError($attributeName, self::RULE_MIN, $rule);
                    }

                    if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                        $this->addError($attributeName, self::RULE_MAX, $rule);
                    }

                    if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}->value) {
                        $this->addError($attributeName, self::RULE_MATCH, $rule);
                    }
                    if ($ruleName === self::RULE_UNIQUE) {
                        $className = $rule['class'];
                        $uniqueAttr = $attributeName;
                        $tableName = $className::DB_TABLE;
                        $statement = Application::current()->db->pdo->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                        $statement->execute([':attr' => $value]);
                        $record = $statement->fetchObject();
                        if ($record) {
                            $this->addError($attributeName, self::RULE_UNIQUE, ['field' => $attributeName]);
                        }
                    }
                }
            }
        }
        return empty($this->errors);
    }

    public function addError(string $attributeName, string $rule, $params = [])
    {
        $message = $rule;
        if(Config::HIDE_FORM_FIELD_RULE_DEBUG_TEXT) {
            $message = preg_replace('/\[[^\]]*\]/', '', $message);
        }
        foreach ($params as $key => $value) { // replace the placeholder {key} with value
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attributeName][] = $message;
    }

    public function hasError($attributeName)
    {
        return $this->errors[$attributeName] ?? false;
    }
    public function getFirstError($attributeName)
    {
        return $this->errors[$attributeName][0] ?? false;
    }

}


class FormModelField
{
    public const TYPE_TEXT = 'text';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';
    public const TYPE_EMAIL = 'email';
    public const TYPE_DATE = 'date';

    public $value;
    public string $type;
    public string $label;
    public array $rules = [];

    public function __construct($type = self::TYPE_TEXT, $label = "", $rules = [])
    {
        $this->type = $type;
        $this->rules = $rules;

        if($label !== "") {
            $this->label = $label;
        }
    }
}
