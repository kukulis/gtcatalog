<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.9.4
 * Time: 21.17
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Services\PicturesService;
use Gt\Catalog\Services\ProductsService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PicturesController  extends AbstractController
{

    public function uploadPicture(Request $r, LoggerInterface $logger, PicturesService $picturesService, ProductsService $productsService) {
        try {
            $sku = $r->get('sku', 0);

            $product = $productsService->getProduct($sku); // jei užkrauna tai ok, o jei ne
            if ($product == null) {
                throw new CatalogErrorException('Cant load product with sku=' . $sku);
            }

            /** @var UploadedFile $pictureFile */
            $pictureFile = $r->files->get('picture');

            if ($pictureFile == null) {
                throw new CatalogErrorException('Picture file is not given');
            }
            $picture = $picturesService->createPicture($pictureFile->getRealPath(), $pictureFile->getClientOriginalName());

            $picturesService->assignPictureToProduct($product, $picture);
            return new Response('Uploaded picture with id=' . $picture->getId() . ' and name '.$picture->getName() );

            // TODO redirect ?
        } catch ( CatalogErrorException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }

    }


}