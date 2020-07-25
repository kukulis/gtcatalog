<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 15.17
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Form\ProductFormType;
use Gt\Catalog\Services\ProductsService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        $page = $request->get('page', 0);

        $products = $productsService->getProducts($page);

        return $this->render('@Catalog/products/list.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @param Request $request
     * @param string $sku
     * @param ProductsService $productsService
     * @return Response
     * @throws \Gt\Catalog\Exception\CatalogErrorException
     */
    public function editAction(Request $request, $sku, ProductsService $productsService ) {
        $product = $productsService->getProduct( $sku );


        if ( $product == null ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => 'Product not loaded by sku '.$sku,
            ]);
        }

        // TODO load product language
        $productLanguage = new ProductLanguage();

        // TODO

//        $language = new Language();

        $productFormType = new ProductFormType();
        $productFormType->setProduct($product);
        $productFormType->setProductLanguage( $productLanguage);
        $productFormType->setSelectedLanguage('lt');

        $form = $this->createForm(ProductFormType::class, $productFormType );

        return $this->render('@Catalog/products/edit.html.twig', [
//            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Called from edit form
     * @param Request $request
     * @return Response
     */
    public function updateAction(Request $request) {
        // TODO redirect to list or to error page
        return new Response('TODO update Action');
    }

    public function newAction () {
        // TODO create new record into database and redirect to edit
        return new Response('TODO new product' );
    }

    public function deleteAction() {
        return new Response('TODO delete product' );
    }

}