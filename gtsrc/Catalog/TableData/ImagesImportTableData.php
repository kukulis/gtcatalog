<?php

namespace Gt\Catalog\TableData;

class ImagesImportTableData
{
    public function getTableData(array $rows): TableData
    {
        return new TableData($rows, $this->getColumns(), $this->getTableOptions());
    }

    private function getColumns(): array
    {
        $columns = [
            ['name' => 'ID', 'property' => 'id'],
            ['name' => 'Created At', 'property' => 'createdTime', 'type' => 'datetime'],
            ['name' => 'Name', 'property' => 'name'],
            ['name' => 'Status', 'property' => 'status'],
            ['name' => 'Start time', 'property' => 'startTime', 'type' => 'datetime'],
            ['name' => 'Finish time', 'property' => 'finishedTime', 'type' => 'datetime'],
            ['name' => 'Total pictures', 'property' => 'totalPictures'],
            ['name' => 'Imported pictures', 'property' => 'importedPictures'],
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
                    'title' => 'View/Edit',
                    'icon' => 'fas fa-edit',
                    'route' => 'gt.catalog.job_view',
                    'routeParams' => ['id' => 'id'],
                ],
                [
                    'title' => 'Delete',
                    'icon' => 'fas fa-trash-alt',
                    'route' => 'gt.catalog.job_delete',
                    'routeParams' => ['id' => 'id'],
                    'confirm' => 'Do you really want to delete selected job?',
                ],
            ],
            'width' => '10%',
        ];
    }

    private function getTableOptions(): array
    {
        return [
            'tableClass' => 'table table-bordered',
        ];
    }
}