<?php

namespace core;

use DateTime;
use ReflectionClass;
use ReflectionProperty;

// Abstract class for SQL models
// Implements basic CRUD operations
abstract class SqlModel
{
    public const INT_DEFAULT_VALUE = -1;
    public const STRING_DEFAULT_VALUE = 'unset';
    public const DATETIME_DEFAULT_VALUE = '0000-00-00 00:00:00';

    public int $id = SqlModel::INT_DEFAULT_VALUE;

    const tableName = "";

    public function __construct()
    {
        // Set default values for class attributes
        // If values are left unset they will not be visible in the function property_exists
        $reflectionClass = new ReflectionClass($this);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyType = $property->getType();

            if ($propertyType) {
                $typeName = $propertyType->getName();

                if (!isset($this->{$propertyName})) {
                    switch ($typeName) {
                        case 'int':
                            $this->{$propertyName} = self::INT_DEFAULT_VALUE;
                            break;
                        case 'string':
                            $this->{$propertyName} = self::STRING_DEFAULT_VALUE;
                            break;
                        case 'DateTime':
                            $this->{$propertyName} = new DateTime(self::DATETIME_DEFAULT_VALUE);
                            break;
                        case 'bool':
                            $this->{$propertyName} = false;
                            break;
                        default:
                            break;
                    }
                }
            }
        }
    }

    public function loadDataFromArray($data) // Load data from asociative array; Recieve a dictionary and adds it's value to the class attributes 
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if ($this->{$key} instanceof DateTime) {
                    if (gettype($value) === 'string') {
                        $this->{$key} = new DateTime($value);
                    } else {
                        $this->{$key} = $value;
                    }
                }
                else if (gettype($this->{$key}) === 'string' and $value === null) {
                    $this->{$key} = self::STRING_DEFAULT_VALUE;
                }
                else {
                    $this->{$key} = $value;
                }
            }
        }
    }

    public function loadDataFromDb($_id = "") // This method can be extended
    {
        if($_id !== "") 
            $id = $_id;
        
        $sql = "SELECT * FROM " . static::tableName . " WHERE id = :id";
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->loadDataFromArray($data);
    }

    public static function getById(int $id)
    {
        $sql = "SELECT * FROM " . static::tableName . " WHERE id = :id";
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            $instance = new static();
            $instance->loadDataFromArray($data);
            return $instance;
        }
        return null;
    }

    public static function getByWhere(string $where, array $params = [])
    {
        $sql = "SELECT * FROM " . static::tableName . " WHERE $where";
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $instances = [];
        foreach ($data as $row) {
            $instance = new static();
            $instance->loadDataFromArray($row);
            $instances[] = $instance;
        }
        return $instances;
    }

    public function save()
    {
        if ($this->id === self::INT_DEFAULT_VALUE) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    public function insert()
    {
        // Get atributes of the class 
        $data = get_object_vars($this);
        foreach ($data as $key => $value) {
            if ($value instanceof DateTime) {
                $data[$key] = $value->format('Y-m-d H:i:s');
            } else if ($key === 'tableName' or $key === 'id') {
                unset($data[$key]);
            }
        }


        $sql = "INSERT INTO " . static::tableName . " (";

        $sql .= implode(', ', array_keys($data));
        $sql .= ") VALUES (";
        $sql .= implode(', ', array_map(fn($key) => ":$key", array_keys($data)));
        $sql .= ")";
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->execute($data);
    }

    public function update()
    {
        $data = get_object_vars($this);
        foreach ($data as $key => $value) {
            if ($value instanceof DateTime) {
                $data[$key] = $value->format('Y-m-d H:i:s');
            } else if ($key === 'tableName' or $key === 'id') {
                unset($data[$key]);
            }
        }

        $sql = "UPDATE " . static::tableName . " SET ";
        $sql .= implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
        $sql .= " WHERE id = :id";
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->execute($data);
    }

    public static function delete(string $id)
    {
        $sql = "DELETE FROM " . static::tableName . " WHERE id = :id";
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    // Get a column from the table; For the params use :paramName
    public static function getColumn($column, $where = "", $params = [])
    {
        if ($where === "") {
            $sql = "SELECT $column FROM " . static::tableName;
        } else {
            $sql = "SELECT $column FROM " . static::tableName . " WHERE $where";
        }
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->bindParam(':col', $column, \PDO::PARAM_STR);
        try {
            $stmt->execute($params);
        } catch (\Exception $e) {
            return [];
        }
        $data = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        return $data;
    }

    // Get a column with the id; Return asociative array with id and column
    public static function getColumnWithId($column, $where = "", $params = [])
    {
        if ($where === "") {
            $sql = "SELECT id, $column FROM " . static::tableName;
        } else {
            $sql = "SELECT id, $column FROM " . static::tableName . " WHERE $where";
        }
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->bindParam(':column', $column, \PDO::PARAM_STR);
        try {
            $stmt->execute($params);
        } catch (\Exception $e) {
            return [];
        }
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }
}
