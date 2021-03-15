<?php
/**
 * BrandsController.php
 * Created by Giedrius Tumelis.
 * Date: 2021-03-15
 * Time: 12:22
 */

namespace Gt\Catalog\Controller;


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

    public function editAction() {
        return new Response('TODO editAction' );
    }

    public function addAction() {
        return new Response('TODO addAction' );
    }
}