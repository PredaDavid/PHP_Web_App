<?php

namespace core;

use DateTime;
use ReflectionClass;
use ReflectionProperty;

// Abstract class for SQL models
// Implements basic CRUD operations
abstract class SqlModel
{
    // Default values for class attributes
    public const INT_DEFAULT_VALUE = -1;
    public const STRING_DEFAULT_VALUE = 'unset';
    public const DATETIME_DEFAULT_VALUE = '0000-00-00 00:00:00';

    const TABLE_NAME = ""; // Table name in the database
    const EXTRA_ATTRIBUTES = []; // Extra attributes that are not in the table and should be ignored on CRUD operations

    public int $id = SqlModel::INT_DEFAULT_VALUE; // All models should have an id as primary key

    public function __construct()
    {
        // Set default values for class attributes
        // If values are not set they will not be visible when we call function property_exists
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

    /**
     * Load data from an associative array in this object. 
     * Can manage DateTime objects both as strings and as DateTime objects.
     * @param array $data The data to load
     * @return void
     */
    private function loadDataFromArray($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if ($this->{$key} instanceof DateTime) {
                    if (gettype($value) === 'string') {
                        $this->{$key} = new DateTime($value);
                    } else {
                        $this->{$key} = $value;
                    }
                } else if (gettype($this->{$key}) === 'string' and $value === null) {
                    $this->{$key} = self::STRING_DEFAULT_VALUE;
                } else {
                    $this->{$key} = $value;
                }
            }
        }
    }

    /**
     * Loads data from the database to this object.
     * This method should be extended for every model, that has extra attributes.
     * The method should call the parent method and then load the extra attributes.
     * Use the $_id parameter to load data for a specific ID.
     * @param int $_id The ID of the record to load. If not provided, it defaults to the value of $this->id.
     * @return void
     */
    public function loadDataFromDb(int $_id = -1)
    {
        if ($_id !== -1)
            $this->id = $_id;

        $sql = "SELECT * FROM " . static::TABLE_NAME . " WHERE id = :id";
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            Response::error("No data found for id $this->id in table " . static::TABLE_NAME);
        }
        $this->loadDataFromArray($data);
    }

    /**
     * Saves the current model instance to the database.
     * If the instance is new (id is equal to the default value), it will be inserted.
     * Otherwise, the existing record will be updated.
     * @return void
     */
    public function save()
    {
        if ($this->id === self::INT_DEFAULT_VALUE) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    /**
     * Inserts the current object's data into the database, excluding specified attributes.
     * This method constructs an SQL INSERT statement based on the object's properties,
     * ignoring any attributes specified in the $toIgnore array, as well as any extra
     * attributes defined in the static::EXTRA_ATTRIBUTES constant and the 'id' attribute.
     * @param array $toIgnore An array of attribute names to be ignored during the insert operation.
     */
    private function insert(array $toIgnore = [])
    {
        // Get atributes of the class 
        $data = get_object_vars($this);
        foreach ($data as $key => $value) {
            if (in_array($key, $toIgnore)) { // Ignore parameters
                unset($data[$key]);
            } else if (in_array($key, static::EXTRA_ATTRIBUTES)) { // Ignore extra attributes
                unset($data[$key]);
            } else if ($value instanceof DateTime) {
                $data[$key] = $value->format('Y-m-d H:i:s');
            } else if ($key === 'id') { // Ignore id
                unset($data[$key]);
            }
        }

        $sql = "INSERT INTO " . static::TABLE_NAME . " (";
        $sql .= implode(', ', array_keys($data));
        $sql .= ") VALUES (";
        $sql .= implode(', ', array_map(fn($key) => ":$key", array_keys($data)));
        $sql .= ")";
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->execute($data);
    }

    /**
     * Updates the current object's data in the database, excluding specified attributes.
     * This method constructs an SQL UPDATE statement based on the object's properties,
     * ignoring any attributes specified in the $toIgnore array, as well as any extra
     * attributes defined in the static::EXTRA_ATTRIBUTES constant.
     * @param array $toIgnore An array of attribute names to be ignored during the update operation.
     */
    private function update(array $toIgnore = [])
    {
        $data = get_object_vars($this);
        foreach ($data as $key => $value) {
            if (in_array($key, $toIgnore)) {
                unset($data[$key]);
            } else if (in_array($key, static::EXTRA_ATTRIBUTES)) { // Ignore extra attributes
                unset($data[$key]);
            } else if ($value instanceof DateTime) {
                $data[$key] = $value->format('Y-m-d H:i:s');
            } else if ($key === 'id') { // Ignore id so we don't accidentally update it
                unset($data[$key]);
            }
        }


        $sql = "UPDATE " . static::TABLE_NAME . " SET ";
        $sql .= implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
        $sql .= " WHERE id = :id";
        $stmt = Application::current()->db->pdo->prepare($sql);
        $data['id'] = $this->id; // Add the id again so we can use it in the WHERE clause
        $stmt->execute($data);
    }

    /**
     * Deletes the current object from the database.
     * @return void
     */
    public function delete()
    {
        $sql = "DELETE FROM " . static::TABLE_NAME . " WHERE id = :id";
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Get all records from the table and return them as instances of the current class.
     * @return array An array of instances of the current class.
     */
    public static function getByWhere(string $where, array $params = [])
    {
        $sql = "SELECT * FROM " . static::TABLE_NAME . " WHERE $where";
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

    /**
     * Retrieves a specific column from the table.
     * If a WHERE clause is provided, it will be included in the query.
     *
     * @param string $column The name of the column to retrieve.
     * @param string $where Optional. The WHERE clause to filter the results.
     * @param array $params Optional. An associative array of parameters to bind to the query.
     * @return array The values of the specified column.
     */
    public static function getColumn($column, $where = "", $params = [])
    {
        if ($where === "") {
            $sql = "SELECT $column FROM " . static::TABLE_NAME;
        } else {
            $sql = "SELECT $column FROM " . static::TABLE_NAME . " WHERE $where";
        }
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->bindParam(':col', $column, \PDO::PARAM_STR);
        try {
            $stmt->execute($params);
        } catch (\Exception $e) {
            Response::error("Error executing query: ", $e->getMessage());
        }
        $data = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        return $data;
    }

    /**
     * Retrieves a specific column along with the id. defined by the static::TABLE_NAME constant.
     * If a WHERE clause is provided, it will be included in the query.
     * @param string $column The name of the column to retrieve.
     * @param string $where Optional. The WHERE clause to filter the results.
     * @param array $params Optional. An associative array of parameters to bind to the query.
     * @return array An associative array with the id and the specified column values.
     */
    public static function getColumnWithId($column, $where = "", $params = [])
    {
        if ($where === "") {
            $sql = "SELECT id, $column FROM " . static::TABLE_NAME;
        } else {
            $sql = "SELECT id, $column FROM " . static::TABLE_NAME . " WHERE $where";
        }
        $stmt = Application::current()->db->pdo->prepare($sql);
        $stmt->bindParam(':column', $column, \PDO::PARAM_STR);
        try {
            $stmt->execute($params);
        } catch (\Exception $e) {
            Response::error("Error executing query: ", $e->getMessage());
        }
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }
}
