<?php

namespace Gt\Catalog\TableData;

use phpDocumentor\Reflection\Type;

class ProductLogTableData
{
    public function getTableData(array $rows): TableData
    {
        return new TableData($rows, $this->getColumns(), $this->getTableOptions());
    }

    private function getColumns(): array
    {
        return [
            ['name' => 'Sku', 'property' => 'sku'],
            ['name' => 'Product diff', 'property' => 'getProductDiff'],
            ['name' => 'Language diff', 'property' => 'getLanguageDiff'],
            ['name' => 'Language', 'property' => 'language'],
            ['name' => 'User', 'property' => 'username'],
            ['name' => 'Date', 'property' => 'dateCreated', 'type' => 'datetime'],
        ];
    }

    private function getTableOptions(): array
    {
        return [
            'tableClass' => 'table table-bordered',
        ];
    }
}