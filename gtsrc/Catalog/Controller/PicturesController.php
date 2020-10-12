<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.9.4
 * Time: 21.17
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Services\PicturesService;
use Gt\Catalog\Services\ProductsService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PicturesController  extends AbstractController
{

    public function uploadPicture(Request $r, LoggerInterface $logger, PicturesService $picturesService, ProductsService $productsService) {
        // TODO

        $sku=$r->get('sku', 0 );

        $product = $productsService->getProduct($sku); // jei užkrauna tai ok, o jei ne
        if ( $product == null ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => 'Cant load product with sku='.$sku,
            ]);
        }

        return new Response('TODO upload picture for '.$sku );
    }


}