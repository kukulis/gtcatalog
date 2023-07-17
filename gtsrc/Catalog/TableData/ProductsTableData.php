<?php

namespace Gt\Catalog\TableData;

class ProductsTableData
{
    public function getTableData(array $rows): TableData
    {
        return new TableData($rows, $this->getColumns(), $this->getTableOptions());
    }

    private function getColumns(): array
    {
        $columns = [
            ['name' => 'SKU', 'property' => 'sku', 'width' => '10%'],
            ['name' => 'Last update', 'property' => 'lastUpdate', 'type' => 'datetime', 'width' => '20%'],
            ['name' => 'Version', 'property' => 'version', 'width' => '5%'],
            ['name' => 'Name', 'property' => 'extractedName', 'width' => '35%'],
            ['name' => 'Brand', 'property' => 'brand', 'width' => '15%'],
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
                    'route' => 'gt.catalog.product_edit',
                    'routeParams' => ['sku' => 'sku'],
                ],
                [
                    'title' => 'Pictures',
                    'icon' => 'fas fa-image',
                    'route' => 'gt.catalog.product_pictures',
                    'routeParams' => ['sku' => 'sku'],
                ],
                [
                    'title' => 'Categories',
                    'icon' => 'fas fa-list',
                    'route' => 'gt.catalog.product_categories_edit_form',
                    'routeParams' => ['sku' => 'sku'],
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