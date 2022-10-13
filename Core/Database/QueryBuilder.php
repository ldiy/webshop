<?php

namespace Core\Database;

use Core\Exceptions\QueryBuilderException;
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
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new QueryBuilderException("Error with query: " . $this->query, (int)$e->getCode(), $e);
        }
    }

    /**
     * Execute the query as a "select" statement and return the first result.
     *
     * @return array
     */
    public function first(): mixed
    {
        $this->limit(1);
        $results = $this->get();
        return $results ? $results[0] : [];
    }

    /**
     * Find a record by its primary key.
     *
     * @param int $id
     * @return array
     */
    public function find(int $id): mixed
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
        $this->query = "SELECT " . $columns . " FROM " . $this->table;
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
        $this->query = 'INSERT INTO ' . $this->table . ' (' . $columns . ') VALUES ('. $values . ')';
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
        $this->query = 'UPDATE ' . $this->table . ' SET ' . $columns;
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
                }
            } else {
                $this->query .= $where['column'] . ' ' . $where['operator'] . ' ?';
                $this->bindings[] = $where['value'];
            }
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