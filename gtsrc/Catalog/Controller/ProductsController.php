<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 15.17
 */

namespace Gt\Catalog\Controller;

use Gt\Catalog\Exception\CatalogDetailedException;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Exception\WrongAssociationsException;
use Gt\Catalog\Form\ProductFormType;
use Gt\Catalog\Form\ProductsFilterType;
use Gt\Catalog\Services\ProductsService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends AbstractController
{

    /**
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request, LoggerInterface $logger, ProductsService $productsService ) {
        $logger->info ( 'listAction called');

        $productsFilterType = new ProductsFilterType();
        $filterForm = $this->createForm( ProductsFilterType::class, $productsFilterType);
        $filterForm->handleRequest($request);

        $products = $productsService->getProducts($productsFilterType);

        $languageCode = $productsFilterType->getLanguageCode();

        return $this->render('@Catalog/products/list.html.twig', [
            'products' => $products,
            'languageCode'  => $languageCode,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $sku
     * @param ProductsService $productsService
     * @return Response
     * @throws CatalogErrorException
     * @throws CatalogDetailedException
     */
    public function editAction(Request $request, $sku, $languageCode, ProductsService $productsService ) {

        $messages = [];
        $message = '';
        $suggestions =[];
        $allLanguages = [];

        try {
            $product = $productsService->getProduct( $sku );

            if ( $product == null ) {
                return $this->render('@Catalog/error/error.html.twig', [
                    'error' => 'Product not loaded by sku '.$sku,
                ]);
            }
            $productLanguage = $productsService->getProductLanguage($sku, $languageCode);

            $productFormType = new ProductFormType();
            $productFormType->setProduct($product);
            $productFormType->setProductLanguage($productLanguage);


            $allLanguages = $productsService->getAllLanguages();

            $form = $this->createForm(ProductFormType::class, $productFormType);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                /** @var SubmitButton $saveSubmit */
                $saveSubmit = $form->get('save');

                if ($saveSubmit->isSubmitted()) {
                    $productsService->storeProduct($product);
                    $productsService->storeProductLanguage ($productLanguage);
                }
            }

        } catch ( WrongAssociationsException $e ) { // paveldi iš CatalogDetailedException
            $message = $e->getMessage();
            $messages = $e->getDetails();
            $objects = $e->getRelatedObjects();

            $suggestions = $productsService->getSuggestions ( $objects );
        }
        catch ( CatalogErrorException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }

        return $this->render('@Catalog/products/edit.html.twig', [
            'form' => $form->createView(),
            'messages' => $messages,
            'message' => $message,
            'suggestions' => $suggestions,
            'languages' => $allLanguages,
            'sku' => $sku,
            'languageCode' => $languageCode,
        ]);
    }

    public function deleteAction() {
        return new Response('TODO delete product' );
    }

    public function picturesList(Request $request, $sku, ProductsService $productsService) {
        // show assigned pictures list and picture form
        $product = $productsService->getProduct($sku);
        // TODO load

        return $this->render('@Catalog/products/pictures.html.twig',
            [
                'product' => $product
        ] );

    }

    public function addPictureForm(Request $request, $sku, ProductsService $productsService) {
        $product = $productsService->getProduct($sku);
        // TODO load

        return $this->render('@Catalog/pictures/add.html.twig',
            [
                'product' => $product
            ] );
    }

    public function uploadPicture() {

    }


    public function importProductsFormAction() {
        return $this->render('@Catalog/products/import_form.html.twig', []);
    }

    public function importProductsAction(Request $r, ProductsService $productsService) {
        /** @var File $csvFileObj */
        $csvFileObj  = $r->files->get('csvfile');
        $file = $csvFileObj->getPathname();

        try {
            $count = $productsService->importProducts(  $file );
            return $this->render('@Catalog/products/import_products_results.html.twig',
                [ 'count' => $count,
                ]
            );
        } catch ( CatalogValidateException | CatalogErrorException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

}