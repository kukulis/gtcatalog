<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.9.4
 * Time: 21.17
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Form\PictureFormType;
use Gt\Catalog\Services\PicturesService;
use Gt\Catalog\Services\ProductsService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PicturesController  extends AbstractController
{

    /**
     * @param Request $r
     * @param LoggerInterface $logger
     * @param PicturesService $picturesService
     * @param ProductsService $productsService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function uploadPicture(Request $r, LoggerInterface $logger, PicturesService $picturesService, ProductsService $productsService) {
        $logger->debug('upload picture called' );
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
//            return new Response('Uploaded picture with id=' . $picture->getId() . ' and name '.$picture->getName() );
            return $this->redirect($this->generateUrl('gt.catalog.product_pictures', ['sku'=>$sku]));
        } catch ( CatalogErrorException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }

    }

    /**
     * @param Request $request
     * @param $sku
     * @param ProductsService $productsService
     * @param PicturesService $picturesService
     * @return Response
     * @throws CatalogErrorException
     */
    public function picturesList($sku, ProductsService $productsService, PicturesService $picturesService) {
        // show assigned pictures list and picture form
        $product = $productsService->getProduct($sku);
        $pps = $picturesService->getProductPictures ( $sku );
        return $this->render('@Catalog/products/pictures.html.twig',
            [
                'product' => $product,
                'pps' => $pps,
            ] );
    }

    /**
     * @param string $sku
     * @param int $id_picture
     * @param PicturesService $picturesService
     * @return Response
     */
    public function deletePicture($sku, $id_picture, PicturesService $picturesService) {
        try {
            $picturesService->unassignPicture($sku, $id_picture);
            return $this->redirect($this->generateUrl('gt.catalog.product_pictures', ['sku'=>$sku]));
        } catch (CatalogErrorException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @param $sku
     * @param $id_picture
     * @param PicturesService $picturesService
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function editPicture ( Request $request, $sku, $id_picture, PicturesService $picturesService) {
        // 1 load product picture
        $productPicture = $picturesService->getProductPicture($sku, $id_picture );

        if ( $productPicture == null ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => 'can\'t find '.$id_picture.' for product '.$sku,
            ]);
        }

        try {
            // 2 form
            $formType = new PictureFormType();
            $formType->setProductPicture($productPicture);

            $form = $this->createForm(PictureFormType::class, $formType);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // 3 save
                $picturesService->storeProductPictureWithPicture($productPicture);
                // redirect?
            }

            // 4 view
            // calculate picture path
            $picture = $productPicture->getPicture();
            $path = '/'. $picturesService->calculatePicturePath($picture->getId(), $picture->getName(), '/');
            $picture->setConfiguredPath($path);

            return $this->render('@Catalog/pictures/edit.html.twig',
                [
                    'productPicture' => $productPicture,
                    'form' => $form->createView(),
                ]);
        } catch (CatalogValidateException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}