<?php

namespace Gt\Catalog\TableData;

class UsersTableData
{
    public function getTableData(array $rows): TableData
    {
        return new TableData($rows, $this->getColumns(), $this->getTableOptions());
    }

    private function getColumns(): array
    {
        $columns = [
            ['name' => 'ID', 'property' => 'id'],
            ['name' => 'Login/Email', 'property' => 'email'],
            ['name' => 'Name', 'property' => 'name'],
            ['name' => 'Roles', 'property' => 'rolesstr'],
            ['name' => 'Enabled', 'property' => 'enabled'],
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
                    'route' => 'gt.catalog.users_edit',
                    'routeParams' => ['id' => 'id'],
                ],
            ],
        ];
    }

    private function getTableOptions(): array
    {
        return [
            'tableClass' => 'table table-bordered',
        ];
    }
}