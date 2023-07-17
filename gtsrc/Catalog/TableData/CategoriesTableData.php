<?php

namespace Gt\Catalog\TableData;

use Gt\Catalog\Entity\CategoryLanguage;

class CategoriesTableData
{
    /**
     * @param CategoryLanguage[] $rows
     */
    public function getTableData(array $rows): TableData
    {
        $modifiedRows = [];
        foreach ($rows as $row) {
            $modifiedRows[] = [
                'code' => $row->getCategory()->getCode(),
                'parentCode' => $row->getCategory()->getParentCode(),
                'name' => $row->getName(),
                'description' => $row->getDescription(),
            ];
        }

        return new TableData($modifiedRows, $this->getColumns(), $this->getTableOptions());
    }

    private function getColumns(): array
    {
        $columns = [
            [
                'name' => 'Code',
                'property' => 'code',
                'route' => 'gt.catalog.categories',
                'routeParams' => ['search_category' => 'code']
            ],
            [
                'name' => 'Parent Code',
                'property' => 'parentCode',
                'route' => 'gt.catalog.categories',
                'routeParams' => ['search_category' => 'parentCode']
            ],
            ['name' => 'Name', 'property' => 'name'],
            ['name' => 'Description', 'property' => 'description'],
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
                    'route' => 'gt.catalog.category_edit',
                    'routeParams' => ['code' => 'code'],
                ],
                [
                    'title' => 'Children',
                    'icon' => 'fas fa-sitemap',
                    'route' => 'gt.catalog.categories',
                    'routeParams' => ['search_parent' => 'code'],
                ],
                [
                    'title' => 'Products',
                    'icon' => 'fas fa-list',
                    'route' => 'gt.catalog.categories_assigned_products',
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