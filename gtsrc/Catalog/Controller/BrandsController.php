<?php
/**
 * BrandsController.php
 * Created by Giedrius Tumelis.
 * Date: 2021-03-15
 * Time: 12:22
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Form\BrandsFilterFormType;
use Gt\Catalog\Services\BrandsService;
use Gt\Catalog\Services\TableService;
use Gt\Catalog\TableData\BrandsTableData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BrandsController extends AbstractController
{
    private $tableService;
    private $tableData;

    public function __construct(TableService $tableService, BrandsTableData $tableData)
    {
        $this->tableService = $tableService;
        $this->tableData = $tableData;
    }

    public function listAction(BrandsService $brandsService, Request $request)
    {
        $brandsFilter = new BrandsFilterFormType();
        $form = $this->createForm(BrandsFilterFormType::class, $brandsFilter);
        $form->handleRequest($request);
        $brands = $brandsService->getList($brandsFilter);

        $tableData = $this->tableData->getTableData($brands);

        $tableHtml = $this->tableService->generateTableHtml(
            $tableData->getRows(),
            $tableData->getColumns(),
            $tableData->getTableOptions(),
        );

        return $this->render(
            '@Catalog/brands/list.html.twig',
            [
                'tableHtml' => $tableHtml,
                'filterForm' => $form->createView(),
                'isFilterFormSubmitted' => $form->isSubmitted()
            ]
        );
    }

    public function editAction($id, BrandsService $brandsService, Request $request)
    {
        try {
            $brand = $brandsService->loadBrand($id);
            if ($brand == null) {
                throw new CatalogValidateException('No brand found with id ' . $id);
            }

            $saveButton = $request->get('save');
            $updatedCount = 0;
            if (!empty($saveButton)) {
                $newBrandName = $request->get('brandName');
                $updatedCount = $brandsService->storeBrand($brand, $newBrandName);
            }

            $count = $brandsService->getProductsCount($brand->getBrand());

            return $this->render(
                '@Catalog/brands/edit.html.twig',
                [
                    'brand' => $brand,
                    'id' => $id,
                    'count' => $count,
                    'updatedCount' => $updatedCount,
                ]
            );
        } catch (CatalogValidateException $e) {
            return $this->render(
                '@Catalog/error/error.html.twig',
                [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    public function addAction(BrandsService $brandsService, Request $request)
    {
        try {
            $add = $request->get('add');

            if (!empty($add)) {
                $brandName = $request->get('name');

                $brandsService->addNewBrand($brandName);
                return $this->redirectToRoute('gt.catalog.brands');
            }
            return $this->render(
                '@Catalog/brands/add.html.twig',
                [
                ]
            );
        } catch (CatalogValidateException $e) {
            return $this->render(
                '@Catalog/error/error.html.twig',
                [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    public function removeAction($id, BrandsService $brandsService)
    {
        try {
            $brandsService->removeBrand($id);
            return $this->redirectToRoute('gt.catalog.brands');
        } catch (CatalogValidateException $e) {
            return $this->render(
                '@Catalog/error/error.html.twig',
                [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }
}