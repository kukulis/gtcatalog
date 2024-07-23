<?php

namespace Gt\Catalog\Controller;

use Gt\Catalog\Entity\ProductLog;
use Gt\Catalog\Form\ProductLogFormType;
use Gt\Catalog\TableData\ProductLogTableData;
use Gt\Catalog\Services\TableService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProductLogController extends AbstractController
{
    private $tableService;
    private $tableData;

    public function __construct(TableService $tableService, ProductLogTableData $tableData)
    {
        $this->tableService = $tableService;
        $this->tableData = $tableData;
    }

    public function listAction(ProductLogService $brandsService, Request $request)
    {
        $brandsFilter = new ProductLogFormType();
        $form = $this->createForm(ProductLogFormType::class, $brandsFilter);
        $form->handleRequest($request);
        $brands = $brandsService->getList($brandsFilter);

        $tableData = $this->tableData->getTableData($brands);

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