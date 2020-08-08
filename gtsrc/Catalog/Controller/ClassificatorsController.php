<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.8
 * Time: 07.30
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Services\ClassificatorsService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ClassificatorsController extends AbstractController
{
    public function listAction(Request $request, LoggerInterface $logger, ClassificatorsService $classificatorsService) {
        return $this->render('@Catalog/classificators/list.html.twig', [

        ]);

    }
}