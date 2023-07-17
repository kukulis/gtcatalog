<?php

namespace Gt\Catalog\TableData;

class TableData
{
    private array $rows;
    private array $columns;
    private array $tableOptions;

    public function __construct(array $rows, array $columns, array $tableOptions)
    {
        $this->rows = $rows;
        $this->columns = $columns;
        $this->tableOptions = $tableOptions;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getTableOptions(): array
    {
        return $this->tableOptions;
    }
}