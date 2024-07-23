<?php

namespace Gt\Catalog\TableData;

class ProductLogTableData
{
    public function getTableData(array $rows): TableData
    {
        return new TableData($rows, $this->getColumns(), $this->getTableOptions());
    }

    private function getColumns(): array
    {
        $columns = [
            ['name' => 'ID', 'property' => 'id'],
            ['name' => 'Name', 'property' => 'brand'],
        ];

        $columns[] = $this->getActionsColumn();

        return $columns;
    }

    private function getActionsColumn(): array
    {
        return [
            'name' => 'Actions',
            'property' => 'actions',
            'actions' => [
                [
                    'title' => 'Edit',
                    'icon' => 'fas fa-edit',
                    'route' => 'gt.catalog.brand_edit',
                    'routeParams' => ['id' => 'id'],
                ],
                [
                    'title' => 'Delete',
                    'icon' => 'fas fa-trash-alt',
                    'route' => 'gt.catalog.brand_delete',
                    'routeParams' => ['id' => 'id'],
                ],
            ],
            'width' => '15%',
        ];
    }

    private function getTableOptions(): array
    {
        return [
            'tableClass' => 'table table-bordered',
        ];
    }
}