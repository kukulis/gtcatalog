<?php


namespace Gt\Catalog\Controller;


use Gt\Catalog\Entity\ClassificatorGroup;
use Gt\Catalog\Form\ClassificatorGroupFormType;
use Gt\Catalog\Services\ClassificatorGroupsService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClassificatorGroupsController extends AbstractController
{
    /**
     * @todo pakurti roles, pvz: ROLE_ADMIN_CLASSIFICATOR_GROUP
     *
     * @Route("/classificator_groups/new", name="classificator_group_new")
     *
     * @param Request $request
     * @param ClassificatorGroupsService $classificatorGroupsService
     */
    public function newAction( Request $request, ClassificatorGroupsService $classificatorGroupsService)
    {
        $form = $this->createForm(ClassificatorGroupFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classificatorGroupsService->newClassificatorGroup($form);
            return $this->redirectToRoute('gt.catalog.classificator_groups');
        }

        return $this->render('@Catalog/classificator_groups/new.html.twig',[
            'classificatorGroupForm' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param LoggerInterface $logger
     * @param ClassificatorGroupsService $classificatorGroupsService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, LoggerInterface $logger, ClassificatorGroupsService $classificatorGroupsService)
    {
        $logger->info('Classificator groups list action called');

        $page = $request->get('page', 0);

        $classificatorGroups = $classificatorGroupsService->getClassificatorGroups($page);

        return $this->render('@Catalog/classificator_groups/list.html.twig', [
            'classificatorGroups' => $classificatorGroups
        ]);
    }

    /**
     * @Route("/classificator_groups/{code}/edit", name="classificator_groups_edit")
     * @param ClassificatorGroup $classificatorGroup
     * @param Request $request
     * @param ClassificatorGroupsService $classificatorGroupsService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(ClassificatorGroup $classificatorGroup, Request $request, ClassificatorGroupsService $classificatorGroupsService)
    {
        $form = $this->createForm(ClassificatorGroupFormType::class, $classificatorGroup);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $classificatorGroupsService->newClassificatorGroup($form);
            return $this->redirectToRoute('gt.catalog.classificator_groups');
        }

        return $this->render('@Catalog/classificator_groups/new.html.twig', [
            'classificatorGroupForm' => $form->createView(),
        ]);
    }
}