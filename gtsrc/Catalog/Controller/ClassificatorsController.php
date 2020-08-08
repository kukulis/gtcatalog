<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.8
 * Time: 07.30
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Form\ClassificatorsListFilterType;
use Gt\Catalog\Services\ClassificatorsService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ClassificatorsController extends AbstractController
{
    public function listAction(Request $request, LoggerInterface $logger, ClassificatorsService $classificatorsService) {

        $classificatorsFilter = new ClassificatorsListFilterType();

        $groups = $classificatorsService->getAllGroups();
        $classificatorsFilter->setAvailableGroups( $groups );
        $form = $this->createForm(ClassificatorsListFilterType::class, $classificatorsFilter);

        $form->handleRequest($request);

        $classificators = $classificatorsService->searchClassificators ( $classificatorsFilter );

        return $this->render('@Catalog/classificators/list.html.twig', [
            'form' => $form->createView(),
            'classificators' => $classificators,
        ]);

    }
}