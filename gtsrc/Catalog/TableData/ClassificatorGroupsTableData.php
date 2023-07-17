<?php

namespace Gt\Catalog\TableData;

class ClassificatorGroupsTableData
{
    public function getTableData(array $rows): TableData
    {
        return new TableData($rows, $this->getColumns(), $this->getTableOptions());
    }

    private function getColumns(): array
    {
        $columns = [
            ['name' => 'Code', 'property' => 'code'],
            ['name' => 'Name', 'property' => 'name'],
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
                    'route' => 'gt.catalog.classificator_group_edit',
                    'routeParams' => ['code' => 'code'],
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