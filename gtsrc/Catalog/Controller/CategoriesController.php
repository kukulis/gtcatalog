<?php

namespace Gt\Catalog\Controller;

use Gt\Catalog\Entity\Category;
use Gt\Catalog\Form\CategoriesFilterType;
use Gt\Catalog\Form\CategoryFormType;
use Gt\Catalog\Services\CategoriesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    /**
     * @Route("/categories/{code}/edit", name="categories_edit")
     *
     * @param Category $category
     * @param Request $request
     * @param CategoriesService $categoriesService
     * @return Response
     */
    public function editAction(Category $category, Request $request, CategoriesService $categoriesService)
    {
        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categoriesService->newCategory($form);
            return $this->redirectToRoute('gt.catalog.categories');
        }

        return $this->render('@Catalog/categories/new.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param CategoriesService $categoriesService
     * @return Response
     */
    public function listAction(Request $request, CategoriesService $categoriesService)
    {
        $categoriesFilter = new CategoriesFilterType();
        $filterForm = $this->createForm( CategoriesFilterType::class, $categoriesFilter);
        $filterForm->handleRequest($request);

        $languageCode = $categoriesFilter->getLanguageCode();

        $categoriesLanguages = $categoriesService->getCategoriesLanguages($categoriesFilter);
        return $this->render('@Catalog/categories/list.html.twig', [
            'categoriesLanguages' => $categoriesLanguages,
            'languageCode'  => $languageCode,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    public function importAction () {
    // TODO
        return new Response('TODO import categories' );
    }
}