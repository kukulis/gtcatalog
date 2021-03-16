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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BrandsController extends AbstractController
{
    public function listAction(BrandsService $brandsService, Request $request ) {

        $brandsFilter = new BrandsFilterFormType();
        $form = $this->createForm(BrandsFilterFormType::class, $brandsFilter );
        $form->handleRequest($request);
        $brands = $brandsService->getList($brandsFilter);

        return $this->render(
            '@Catalog/brands/list.html.twig',
            [
                'brands'=>$brands,
                'filterForm' =>$form->createView(),
            ]
        );
    }

    public function editAction($id, BrandsService $brandsService, Request $request) {
        try {
            $brand = $brandsService->loadBrand($id);
            if ( $brand == null ) {
                throw new CatalogValidateException('No brand found with id '.$id );
            }

            $saveButton = $request->get('save');
            $updatedCount = 0;
            if ( !empty($saveButton )) {
                $newBrandName = $request->get('brandName' );
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
        } catch ( CatalogValidateException $e ) {
            return $this->render(
                '@Catalog/error/error.html.twig',
                [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    public function addAction() {
        return new Response('TODO addAction' );
    }

    public function removeAction($id, BrandsService $brandsService) {
        try {
            $brandsService->removeBrand($id);
            return $this->redirectToRoute('gt.catalog.brands');
        } catch ( CatalogValidateException $e ) {
            return $this->render(
                '@Catalog/error/error.html.twig',
                [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }
}