<?php


namespace Gt\Catalog\Controller;


use Gt\Catalog\Entity\ClassificatorGroup;
use Gt\Catalog\Form\ClassificatorGroupFormType;
use Gt\Catalog\Services\ClassificatorGroupsService;
use Gt\Catalog\Services\TableService;
use Gt\Catalog\TableData\ClassificatorGroupsTableData;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassificatorGroupsController extends AbstractController
{
    private $tableService;
    private $tableData;

    public function __construct(TableService $tableService, ClassificatorGroupsTableData $tableData)
    {
        $this->tableService = $tableService;
        $this->tableData = $tableData;
    }

    /**
     * @todo pakurti roles, pvz: ROLE_ADMIN_CLASSIFICATOR_GROUP
     *
     * @Route("/classificator_groups/new", name="classificator_group_new")
     *
     */
    public function newAction(Request $request, ClassificatorGroupsService $classificatorGroupsService)
    {
        $form = $this->createForm(ClassificatorGroupFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classificatorGroupsService->newClassificatorGroup($form);
            return $this->redirectToRoute('gt.catalog.classificator_groups');
        }

        return $this->render('@Catalog/classificator_groups/new.html.twig', [
            'classificatorGroupForm' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param LoggerInterface $logger
     * @param ClassificatorGroupsService $classificatorGroupsService
     * @return Response
     */
    public function listAction(Request $request, LoggerInterface $logger, ClassificatorGroupsService $classificatorGroupsService)
    {
        $logger->info('Classificator groups list action called');

        $page = $request->get('page', 0);

        $classificatorGroups = $classificatorGroupsService->getClassificatorGroups($page);

        $tableData = $this->tableData->getTableData($classificatorGroups);

        $tableHtml = $this->tableService->generateTableHtml(
            $tableData->getRows(),
            $tableData->getColumns(),
            $tableData->getTableOptions(),
        );

        return $this->render('@Catalog/classificator_groups/list.html.twig', [
            'tableHtml' => $tableHtml
        ]);
    }

    /**
     * @Route("/classificator_groups/{code}/edit", name="classificator_groups_edit")
     * @param ClassificatorGroup $classificatorGroup
     * @param Request $request
     * @param ClassificatorGroupsService $classificatorGroupsService
     * @return RedirectResponse|Response
     */
    public function editAction(ClassificatorGroup $classificatorGroup, Request $request, ClassificatorGroupsService $classificatorGroupsService)
    {
        $form = $this->createForm(ClassificatorGroupFormType::class, $classificatorGroup);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $classificatorGroupsService->newClassificatorGroup($form);
            return $this->redirectToRoute('gt.catalog.classificator_groups');
        }

        return $this->render('@Catalog/classificator_groups/edit.html.twig', [
            'classificatorGroupForm' => $form->createView(),
        ]);
    }
}