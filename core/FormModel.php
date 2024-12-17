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

    public const FORM_SUBMIT_VALUE = 'Submit'; // Default form submit value

    protected array $errors = []; // Array to store form validation errors

    public array $fieldsToIgnore = []; // Fields to ignore when generating the form

    /**
     * Loads data from array to the form model.
     * Assigns values from the array to the corresponding fields.
     * Usually used to load data from $_POST or $_GET.
     * @param array $arr The associative array containing form data.
     * @return void
     */
    public function loadDataFromArray($arr)
    {
        $properties = get_object_vars($this);
        foreach ($properties as $name => $field) {
            if ($field instanceof FormModelField) {
                if ($field->type === FormModelField::TYPE_CHECKBOX) {
                    $field->value = isset($arr[$name]);
                } else if (isset($arr[$name])) {
                    $field->value = $arr[$name];
                }
            }
        }
    }

    /**
     * Loads data from a given SqlModel instance into the current FormModel instance.
     * @param SqlModel $model The SqlModel instance from which to load the data.
     * @return void
     */
    public function loadDataFromSqlModel(SqlModel $model)
    {
        $attributes = get_object_vars($this);
        foreach ($attributes as $name => $attributeValue) {
            if ($attributeValue instanceof FormModelField and isset($model->{$name})) {
                $this->{$name}->value = $model->{$name};
            }
        }
    }

    /**
     * Loads data to an existing SqlModel.
     * This method should be extended for every formModel, that reference a model that has extra attributes.
     * The method should call the parent method and then load the extra attributes.
     * @param SqlModel $model The SqlModel instance to which to load the data.
     * @param array $toIgnore An array of field names to ignore when loading data.
     * @return void
     */
    public function loadDataToSqlModel(SqlModel $model, array $toIgnore = [])
    {
        $attributes = get_object_vars($this);
        foreach ($attributes as $name => $field) {
            if (in_array($name, $toIgnore)) continue; // Skip if the field is in the ignore list
            if (!isset($model->{$name})) continue; // Skip if the attribute does not exist in the model
            if ($field->type === FormModelField::TYPE_CHECKBOX) {
                $model->{$name} = $field->value ? 1 : 0;
            } else if ($field->type === FormModelField::TYPE_PASSWORD) {
                $model->{$name} = password_hash($field->value, PASSWORD_DEFAULT);
            } else {
                $model->{$name} = $field->value;
            }
        }
    }

    /**
     * Validates the form model fields based on their defined rules.
     * @return bool Returns true if there are no validation errors, otherwise false.
     */
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
                        $uniqueAttr = $rule['column'];
                        $tableName = $rule['table'];
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

    /**
     * Generates an HTML form based on the properties of the current object.
     * @return void
     */
    public function generateForm()
    {
        $attributes = get_object_vars($this);
        $submitText = static::FORM_SUBMIT_VALUE;

        // Check if the form will have a file
        $hasImage = false;
        foreach ($attributes as $name => $attributeValue) {
            if ($attributeValue instanceof FormModelField and $attributeValue->type === FormModelField::TYPE_FILE) {
                $hasImage = true;
                break;
            }
        }

        if ($hasImage)
            echo "<form action='' method='POST' enctype='multipart/form-data'>";
        else
            echo "<form action='' method='POST' >";

        foreach ($attributes as $name => $attributeValue) {
            if (in_array($name, $this->fieldsToIgnore)) {
                continue;
            }
            if ($attributeValue instanceof FormModelField) {
                echo $attributeValue;
            }
        }
        echo "<input type='submit' value='$submitText' name='$submitText'>";
        echo "</form>";
    }

    /**
     * Adds an error message for a specific form field.
     * @param string $name The name of the form field.
     * @param string $rule The validation rule that was violated or a message to display.
     * @param array $params Optional parameters to replace placeholders in the rule message.
     */
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

    /**
     * Checks if there is an error for a specific form field.
     * @param string $name The name of the form field.
     * @return bool|array Returns false if no error exists, otherwise returns the error array.
     */
    public function hasError($name)
    {
        return $this->errors[$name] ?? false;
    }

    /**
     * Retrieves the first error message for a specific form field.
     * @param string $name The name of the form field.
     * @return bool|string Returns false if no error exists, otherwise returns the first error message.
     */
    public function getFirstError($name)
    {
        return $this->errors[$name][0] ?? false;
    }
}
