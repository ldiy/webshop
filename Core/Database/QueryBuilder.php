<?php

namespace Core\Database;

use Core\Exceptions\QueryBuilderException;
use Core\Model\Model;
use http\Exception\RuntimeException;
use PDO;

class QueryBuilder
{
    private DB $connection;

    /**
     * The table on which the query is being performed.
     *
     * @var string
     */
    private string $table;

    /**
     * The columns that should be returned.
     *
     * @var array
     */
    private array $columns;

    /**
     * The where constraints for the query.
     *
     * @var array
     */
    private array $wheres;

    /**
     * The joins for the query.
     *
     * @var array
     */
    private array $joins;

    /**
     * Valid values: 'and', 'or'
     *
     * @var string
     */
    private string $whereBoolean = 'and';

    /**
     * The orderings for the query.
     *
     * @var array
     */
    private array $orders;

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    private int $limit;

    /**
     * The building query.
     *
     * @var string
     */
    private string $query;

    /**
     * Parameters for the query.
     *
     * @var array
     */
    private array $bindings = [];

    /**
     * The primary key of the table.
     *
     * @var string
     */
    private string $primaryKey;

    /**
     * The model class that should be returned.
     *
     * @var string
     */
    private string $model;


    /**
     * Create a new query builder instance.
     *
     * @param DB $connection
     * @param string $table
     * @param string $primaryKey
     */
    public function __construct(DB $connection, string $table, string $primaryKey = 'id')
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    public function withModel(string $modelClass): self
    {
        $this->model = $modelClass;
        return $this;
    }

    /**
     * Set the columns to be selected.
     * By default, all columns are selected.
     *
     * @param  array  $columns
     * @return $this
     */
    public function select(array $columns = ['*']): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Add a where clause to the query.
     * If multiple where clauses are added, they will be combined by and.
     * To combine them by or, use orWhere().
     *
     * Valid operators: =, <>, <, >, <=, >=, like, not like
     *
     * @param string $column
     * @param string $operator
     * @param string $value
     * @return $this
     */
    public function where(string $column, string $operator, string $value): self
    {
        $this->wheres[] = compact('column', 'operator', 'value');
        return $this;
    }

    /**
     * Add a where clause to the query.
     * If multiple where clauses are added, they will be combined by or.
     *
     * Valid operators: =, <>, <, >, <=, >=, like, not like
     *
     * @param string $column
     * @param string $operator
     * @param string $value
     * @return $this
     */
    public function orWhere(string $column, string $operator, string $value): self
    {
        $this->whereBoolean = 'or';
        $this->wheres[] = compact('column', 'operator', 'value');
        return $this;
    }

    /**
     * Add a where BETWEEN clause to the query.
     *
     * @param string $column
     * @param string $value1
     * @param string $value2
     * @return $this
     */
    public function whereBetween(string $column, string $value1, string $value2): self
    {
        $type = 'between';
        $this->wheres[] = compact('type', 'column', 'value1', 'value2');
        return $this;
    }

    /**
     * Add a where IN clause to the query.
     *
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function whereIn(string $column, array $values): self
    {
        if (empty($values)) {
            throw new QueryBuilderException('The values array cannot be empty.');
        }
        $type = 'in';
        $this->wheres[] = compact('type', 'column', 'values');
        return $this;
    }

    /**
     * Add a where IS NULL clause to the query.
     *
     * @param string $column
     * @return $this
     */
    public function whereNull(string $column): self
    {
        $type = 'null';
        $this->wheres[] = compact('type', 'column');
        return $this;
    }

    /**
     * Add a where IS NOT NULL clause to the query.
     *
     * @param string $column
     * @return $this
     */
    public function whereNotNull(string $column): self
    {
        $type = 'not null';
        $this->wheres[] = compact('type', 'column');
        return $this;
    }

    /**
     * Add a (inner) join to the query.
     *
     * @param string $table
     * @param string $column1
     * @param string $operator
     * @param string $column2
     * @return $this
     */
    public function join(string $table, string $column1, string $operator, string $column2): self
    {
        $type = 'inner';
        $this->joins[] = compact('type', 'table', 'column1', 'operator', 'column2');
        return $this;
    }

    /**
     * Add a left join to the query.
     *
     * @param string $table
     * @param string $column1
     * @param string $operator
     * @param string $column2
     * @return $this
     */
    public function leftJoin(string $table, string $column1, string $operator, string $column2): self
    {
        $type = 'left';
        $this->joins[] = compact('type', 'table', 'column1', 'operator', 'column2');
        return $this;
    }

    /**
     * Add a right join to the query.
     *
     * @param string $table
     * @param string $column1
     * @param string $operator
     * @param string $column2
     * @return $this
     */
    public function rightJoin(string $table, string $column1, string $operator, string $column2): self
    {
        $type = 'right';
        $this->joins[] = compact('type', 'table', 'column1', 'operator', 'column2');
        return $this;
    }

    /**
     * Add a full outer join to the query.
     *
     * @param string $table
     * @param string $column1
     * @param string $operator
     * @param string $column2
     * @return $this
     */
    public function fullJoin(string $table, string $column1, string $operator, string $column2): self
    {
        $type = 'full';
        $this->joins[] = compact('type', 'table', 'column1', 'operator', 'column2');
        return $this;
    }


    /**
     * Add an "order by" clause to the query.
     * Multiple orderings can be added.
     *
     * @param string $column
     * @param string $direction Valid values: 'asc', 'desc'
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->orders[] = compact('column', 'direction');
        return $this;
    }

    /**
     * Set the maximum number of records to return.
     *
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @return array
     */
    public function get(): array
    {
        $this->buildSelect();

        try {
            $statement = $this->connection->prepare($this->query);
            $statement->execute($this->bindings);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            // If no model is set, return the result as is.
            if (!isset($this->model)) {
                return $result;
            }

            // Check if the model class exists and is a subclass of Model.
            if (!class_exists($this->model) || !is_subclass_of($this->model, Model::class)) {
                throw new QueryBuilderException("Model class {$this->model} does not exist or does not extend Model class.");
            }
            return array_map(function ($row) {
                return new $this->model($row);
            }, $result);
        } catch (\PDOException $e) {
            throw new QueryBuilderException("Error with query: " . $this->query, (int)$e->getCode(), $e);
        }
    }

    /**
     * Execute the query as a "select" statement and return the first result.
     *
     * @return Model|array|null
     */
    public function first(): Model|array|null
    {
        $this->limit(1);
        $results = $this->get();
        if (!isset($this->model))
            return $results ? $results[0] : [];
        else
            return $results ? $results[0] : null;
    }

    /**
     * Find a record by its primary key.
     *
     * @param int $id
     * @return Model|array|null
     */
    public function find(int $id): Model|array|null
    {
        $this->where($this->primaryKey, '=', $id);
        return $this->first();
    }

    /**
     * Insert a new record into the database.
     *
     * @param array $data Associative array of column => value
     * @return int The id of the inserted record
     */
    public function insert(array $data): int
    {
        $this->buildInsert($data);
        try {
            $statement = $this->connection->prepare($this->query);
            $statement->execute($this->bindings);
            return $this->connection->lastInsertId();
        } catch (\PDOException $e) {
            throw new QueryBuilderException("Error with query: " . $this->query, (int)$e->getCode(), $e);
        }
    }

    /**
     * Update a record in the database.
     *
     * @param array $data associative array of column => value
     * @return bool
     */
    public function update(array $data): bool
    {
        $this->buildUpdate($data);
        try {
            $statement = $this->connection->prepare($this->query);
            return $statement->execute($this->bindings);
        } catch (\PDOException $e) {
            throw new QueryBuilderException("Error with query: " . $this->query, (int)$e->getCode(), $e);
        }
    }

    public function delete(): bool
    {
        $this->buildDelete();
        try {
            $statement = $this->connection->prepare($this->query);
            return $statement->execute($this->bindings);
        } catch (\PDOException $e) {
            throw new QueryBuilderException("Error with query: " . $this->query, (int)$e->getCode(), $e);
        }
    }

    /**
     * Build a select query from all its parts.
     *
     * @return void
     */
    public function buildSelect(): void
    {
        if (empty($this->columns)) {
            $this->columns = ['*'];
        }
        $columns = implode(', ', $this->columns);
        $this->query = 'SELECT ' . $columns . ' FROM `' . $this->table . '`';
        $this->buildWheres()
            ->buildOrders()
            ->buildLimit();
    }

    /**
     * Build a insert query.
     *
     * @param array $data
     * @return void
     */
    private function buildInsert(array $data): void
    {
        $columns = implode(', ', array_keys($data));
        $values = str_repeat('?, ', count($data) - 1) . '?';
        $this->query = 'INSERT INTO `' . $this->table . '` (' . $columns . ') VALUES ('. $values . ')';
        $this->bindings = array_values($data);
    }

    /**
     * Build a update query.
     *
     * @param array $data
     * @return void
     */
    private function buildUpdate(array $data): void
    {
        $columns = implode(' = ?, ', array_keys($data)) . ' = ?';
        $this->query = 'UPDATE `' . $this->table . '` SET ' . $columns;
        $this->bindings = array_values($data);
        $this->buildWheres();
    }

    /**
     * Build a delete query.
     *
     * @return void
     */
    private function buildDelete(): void
    {
        $this->query = 'DELETE FROM ' . $this->table;
        $this->buildWheres();
    }

    /**
     * Build the where clauses of the query.
     *
     * @return $this
     */
    private function buildWheres(): static
    {
        if (empty($this->wheres)) {
            return $this;
        }
        $this->query .= ' WHERE ';
        foreach ($this->wheres as $key => $where) {
            if ($key > 0) {
                $this->query .= ' ' . $this->whereBoolean . ' ';
            }
            if (isset($where['type'])) {
                if ($where['type'] === 'between') {
                    $this->query .= $where['column'] . ' BETWEEN ? AND ?';
                    $this->bindings[] = $where['value1'];
                    $this->bindings[] = $where['value2'];
                } elseif ($where['type'] === 'in') {
                    $repeatCnt = max(count($where['values']) - 1, 0);
                    $this->query .= $where['column'] . ' IN (' . str_repeat('?, ', $repeatCnt) . '?)';
                    $this->bindings = array_merge($this->bindings, $where['values']);
                } elseif ($where['type'] === 'null') {
                    $this->query .= $where['column'] . ' IS NULL';
                } elseif ($where['type'] === 'not null') {
                    $this->query .= $where['column'] . ' IS NOT NULL';
                }
            } else {
                $this->query .= $where['column'] . ' ' . $where['operator'] . ' ?';
                $this->bindings[] = $where['value'];
            }
        }

        return $this;
    }

    /**
     * Build the join part of the query.
     * @todo add table alias to the column names?
     *
     * @return $this
     */
    private function buildJoins(): static
    {
        if (empty($this->joins)) {
            return $this;
        }
        foreach ($this->joins as $join) {
            $this->query .= ' ' . $join['type'] . ' JOIN ' . $join['table'] . ' ON ' . $join['column1'] . ' ' . $join['operator'] . ' ' . $join['column2'];
        }
        return $this;
    }

    /**
     * Build the order part of the query.
     *
     * @return $this
     */
    private function buildOrders(): static
    {
        if (empty($this->orders)) {
            return $this;
        }
        $this->query .= ' ORDER BY ';
        foreach ($this->orders as $key => $order) {
            if ($key > 0) {
                $this->query .= ', ';
            }
            $this->query .= $order['column'] . ' ' . $order['direction'];
        }

        return $this;
    }

    /**
     * Build the limit part of the query.
     *
     * @return $this
     */
    private function buildLimit(): static
    {
        if (empty($this->limit)) {
            return $this;
        }
        $this->query .= ' LIMIT ' . $this->limit;

        return $this;
    }
}