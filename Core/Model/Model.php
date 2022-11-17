<?php

namespace Core\Model;

use Core\Database\DB;
use Core\Database\QueryBuilder;
use http\Exception\RuntimeException;
use JsonSerializable;

abstract class Model implements JsonSerializable
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
     * @var bool
     */
    static bool $softDelete = false;

    /**
     * @var string
     */
    static string $softDeleteColumn = 'deleted_at';

    /**
     * @var bool
     */
    static bool $timestamps = false;

    /**
     * @var string
     */
    static string $createdAtColumn = 'created_at';

    /**
     * @var string
     */
    static string $updatedAtColumn = 'updated_at';

    /**
     * The attributes of the model.
     * The key is the column name and the value is the value of the column.
     *
     * @var array
     */
    private array $attributes = [];

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
     * Set the value of an attribute.
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value): void
    {
        $this->setAttribute($name, $value);
    }

    /**
     * Serialize the model to an array, so it can be converted to JSON.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
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
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
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
            $this->update($this->getAttributes());
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
        if ($this::$softDelete) {
            $this->update(['deleted_at' => date('Y-m-d H:i:s')]);
        } else {
            DB::table($this::$table)
                ->where($this::$primaryKey, '=', $this->attributes[$this::$primaryKey])
                ->delete();
        }
    }

    /**
     * Update the attributes from this model in the database.
     *
     * @param array $attributes
     * @return void
     */
    private function update(array $attributes): void
    {
        if ($this::$timestamps) {
            $this->setAttribute($this::$updatedAtColumn, date('Y-m-d H:i:s'));
        }
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
        if ($this::$timestamps) {
            $this->setAttribute($this::$createdAtColumn, date('Y-m-d H:i:s'));
            $this->setAttribute($this::$updatedAtColumn, date('Y-m-d H:i:s'));
        }

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
     * @return QueryBuilder
     */
    public static function where(string $column, string $operator, string $value): QueryBuilder
    {
        return DB::table(static::$table)->withModel(static::class)->where($column, $operator, $value);
    }

    /**
     * Get the model for the given primary key from the database.
     *
     * @param int $id
     * @return static|null
     */
    public static function find(int $id): ?self
    {
        $q =  DB::table(static::$table)->withModel(static::class)->where(static::$primaryKey, '=', $id);
        if (static::$softDelete) {
            $q->whereNull('deleted_at');
        }
        return $q->first();
    }

    /**
     * Get all the models from the database.
     *
     * @return array
     */
    public static function all(): array
    {
        $q = DB::table(static::$table)->withModel(static::class);
        if (static::$softDelete) {
            $q->whereNull('deleted_at');
        }
        return $q->get();
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param string $model
     * @param string $foreignKey
     * @param string|null $localKey
     * @return QueryBuilder
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