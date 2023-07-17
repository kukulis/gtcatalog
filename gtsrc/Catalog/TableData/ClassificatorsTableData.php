<?php

namespace Gt\Catalog\TableData;

use Gt\Catalog\Entity\Classificator;

class ClassificatorsTableData
{
    /**
     * @param Classificator[] $rows
     */
    public function getTableData(array $rows): TableData
    {
        $modifiedRows = [];
        foreach ($rows as $row) {
            $modifiedRows[] = [
                'code' => $row->getCode(),
                'group' => $row->getClassificatorGroup()->getName(),
                'value' => $row->getAssignedValue(),
                'customsCode' => $row->getCustomsCode(),
            ];
        }

        return new TableData($modifiedRows, $this->getColumns(), $this->getTableOptions());
    }

    private function getColumns(): array
    {
        $columns = [
            ['name' => 'Code', 'property' => 'code'],
            ['name' => 'Group', 'property' => 'group'],
            ['name' => 'Value', 'property' => 'value'],
            ['name' => 'Customs Code', 'property' => 'customsCode'],
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
                    'route' => 'gt.catalog.classificator_edit',
                    'routeParams' => ['code' => 'code'],
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