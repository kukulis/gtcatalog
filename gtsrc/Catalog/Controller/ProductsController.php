<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 15.17
 */

namespace Gt\Catalog\Controller;


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

    public function editAction(Request $request, $sku ) {



        return new Response('TODO edit product' );
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