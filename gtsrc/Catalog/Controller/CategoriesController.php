<?php


namespace Gt\Catalog\Controller;


use Gt\Catalog\Entity\Category;
use Gt\Catalog\Form\CategoryFormType;
use Gt\Catalog\Services\CategoriesService;
use Psr\Log\LoggerInterface;
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
    public function listAction(Request $request, LoggerInterface $logger, CategoriesService $categoriesService)
    {
        $logger->info('Categories list action called');
        $page = $request->get('page', 0);
        $categories = $categoriesService->getCategories($page);
        return $this->render('@Catalog/categories/list.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/categories/new", name="categories_new")
     *
     * @param Request $request
     * @param CategoriesService $categoriesService
     * @return Response
     */
    public function newAction(Request $request, CategoriesService $categoriesService)
    {
        $form = $this->createForm(CategoryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoriesService->newCategory($form);
            return $this->redirectToRoute('gt.catalog.categories');
        }

        return $this->render('@Catalog/categories/new.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }
}