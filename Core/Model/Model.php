<?php

namespace Core\Model;

use Core\Database\DB;
use Core\Database\QueryBuilder;
use Exception;
use http\Exception\RuntimeException;
use PDO;

abstract class Model
{
    /**
     * The table associated with the model.
     * This should be overridden in the model class.
     *
     * @var string
     */
    static string $table = '';

    /**
     * The primary key for the table associated with the model.
     *
     * @var string
     */
    static string $primaryKey = 'id';

    /**
     * The attributes of the model.
     * The key is the column name and the value is the value of the column.
     *
     * @var array
     */
    private array $attributes = [];

    /**
     * The keys of the attributes array that have been changed compared to the database.
     * TODO: all attributes ara always changed when the model is loaded from the database.
     * @var array
     */
    private array $changedAttributes = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected array $hidden = [];


    public function __construct($attributes = [])
    {
        $this->setAttributes($attributes);
        if (!isset($this::$table)) {
            throw new RuntimeException('Model table not set');
        }
    }

    /**
     * Get the value of an attribute.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }
    
    /**
     * Get this model attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set this model attributes.
     * To sync with the database, call save().
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
        $this->changedAttributes = array_keys($attributes);
    }

    /**
     * Get the value of an attribute.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Set the value of an attribute.
     * To sync with the database, call save().
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
        $this->changedAttributes[] = $key;
    }

    /**
     * Get the attributes that changed compared to the database.
     *
     * @return array
     */
    public function OnlyChangedAttributes(): array
    {
        $attributes = [];
        foreach ($this->changedAttributes as $key) {
            $attributes[$key] = $this->attributes[$key];
        }
        return $attributes;
    }

    /**
     * Get this model attributes without the hidden fields.
     *
     * @return array
     */
    public function toArray(): array
    {
        $attributes = $this->attributes;
        foreach ($this->hidden as $hidden) {
            unset($attributes[$hidden]);
        }
        return $attributes;
    }

    /**
     * Serialize this model to JSON.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Get the primary key of the table related to this model.
     *
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this::$primaryKey;
    }

    /**
     * Sync this model with the database.
     *
     * @return void
     */
    public function save(): void
    {
        if (isset($this->attributes[$this::$primaryKey])) {
            $this->update($this->onlyChangedAttributes());
        } else {
            $this->insert($this->attributes);
        }
    }

    /**
     * Delete this model from the database.
     *
     * @return void
     */
    public function delete(): void
    {
        DB::table($this::$table)
            ->where($this::$primaryKey, '=', $this->attributes[$this::$primaryKey])
            ->delete();
    }

    /**
     * Update the attributes from this model in the database.
     *
     * @param array $attributes
     * @return void
     */
    private function update(array $attributes): void
    {
        DB::table($this::$table)
            ->where($this::$primaryKey, '=', $this->attributes[$this::$primaryKey])
            ->update($attributes);
    }

    /**
     * Insert a new record in the database with the given attributes as values.
     *
     * @param array $attributes
     * @return void
     */
    private function insert(array $attributes): void
    {
        $this->attributes =  $attributes;

        $id = DB::table($this::$table)
            ->insert($attributes);

        $this->attributes[$this::$primaryKey] = $id;
    }

    /**
     * Create a new model instance with the given attributes.
     *
     * @param array $attributes
     * @return static
     */
    public static function create(array $attributes = []): self
    {
        $model = new static();
        $model->setAttributes($attributes);
        $model->save();
        return $model;
    }

    /**
     * Get an array of models that match the given condition.
     *
     * @param string $column
     * @param string $operator
     * @param string $value
     * @return array
     */
    public static function where(string $column, string $operator, string $value): array
    {
        $models = [];
        $result = DB::table(static::$table)->where($column, $operator, $value)->get();
        foreach ($result as $attributes) {
            $models[] = new static($attributes);
        }
        return $models;
    }

    /**
     * Get the model for the given primary key from the database.
     *
     * @param int $id
     * @return static|null
     */
    public static function find(int $id): ?self
    {
        $result = DB::table(static::$table)->where(static::$primaryKey, '=', $id)->first();
        
        if (empty($result)) {
            return null;
        }
        
        return new static($result);
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param string $model
     * @param string $foreignKey
     * @param string|null $localKey
     * @return array
     */
    public function hasMany(string $model, string $foreignKey, string $localKey = null): array
    {
        if (!is_subclass_of($model, Model::class)) {
            throw new RuntimeException('Class must be a subclass of Model');
        }
        $localKey = $localKey ?? $this->getPrimaryKey();
        return $model::where($foreignKey, '=', $this->getAttribute($localKey));
    }

    /**
     * Define a one-to-one relationship.
     *
     * @param string $model
     * @param string $foreignKey
     * @return Model|null
     */
    public function belongsTo(string $model, string $foreignKey): ?Model
    {
        if (!is_subclass_of($model, Model::class)) {
            throw new RuntimeException('Class must be a subclass of Model');
        }
        $foreignKey = $foreignKey ?? $model::$primaryKey;
        return $model::find($this->getAttribute($foreignKey));
    }
}