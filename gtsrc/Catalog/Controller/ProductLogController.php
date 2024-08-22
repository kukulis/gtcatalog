<?php

namespace Gt\Catalog\Controller;

use Gt\Catalog\Form\ProductLogFormType;
use Gt\Catalog\Services\ProductLogService;
use Gt\Catalog\TableData\ProductLogTableData;
use Gt\Catalog\Services\TableService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProductLogController extends AbstractController
{
    private TableService $tableService;
    private ProductLogTableData $tableData;

    public function __construct(TableService $tableService, ProductLogTableData $tableData)
    {
        $this->tableService = $tableService;
        $this->tableData = $tableData;
    }


    public function listAction(ProductLogService $productLogService, Request $request)
    {
        $productLogFilter = new ProductLogFormType();
        $form = $this->createForm(ProductLogFormType::class, $productLogFilter);
        $form->handleRequest($request);
        $productLogs = $productLogService->getList($productLogFilter);

        $tableData = $this->tableData->getTableData($productLogs);

        $tableHtml = $this->tableService->generateTableHtml(
            $tableData->getRows(),
            $tableData->getColumns(),
            $tableData->getTableOptions(),
        );

        return $this->render(
            '@Catalog/product_log/list.html.twig',
            [
                'tableHtml' => $tableHtml,
                'filterForm' => $form->createView(),
                'isFilterFormSubmitted' => $form->isSubmitted()
            ]
        );
    }
}