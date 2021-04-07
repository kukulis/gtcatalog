<?php
/**
 * CustomsKeywordsController.php
 * Created by Giedrius Tumelis.
 * Date: 2021-04-07
 * Time: 12:03
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Form\CustomsKeywordsFormType;
use Gt\Catalog\Services\CustomsKeywordsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CustomsKeywordsController  extends AbstractController
{
    public function listAction(Request $request, CustomsKeywordsService $customsKeywordsService) {
        $filterType = new CustomsKeywordsFormType();

        $form = $this->createForm(CustomsKeywordsFormType::class, $filterType);
        $form->handleRequest($request);


        $customsKeywords = $customsKeywordsService->getKeywords($filterType);
        return $this->render('@Catalog/customs/keywords_list.html.twig', [
            'form' => $form->createView(),
            'customs_keywords' => $customsKeywords,
        ]);
    }

    public function importAction(Request $request) {
        return $this->render('@Catalog/customs/keywords_import.html.twig', [
        ]);
    }
}