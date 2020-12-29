<?php
/**
 * UsersController.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-28
 * Time: 13:25
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Form\UsersFilterFormType;
use Gt\Catalog\Services\UsersService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends AbstractController
{
    public function listAction( Request  $request, LoggerInterface  $logger, UsersService  $usersService ) {


        $logger->debug('listAction called' );

        $filter = new UsersFilterFormType();

        $form = $this->createForm( UsersFilterFormType::class, $filter );
        $form->handleRequest($request);

        $users = $usersService->getFilteredUsers($filter);

        return $this->render('@Catalog/users/list.html.twig', [
            'users' => $users,
            'filterForm' => $form->createView(),
        ]);
    }

    public function editFormAction($id) {
        return $this->render('@Catalog/users/edit.html.twig', [
            'id' => $id,
//            'filterForm' => $form->createView(),
        ]);
    }

    public function updateAction() {
        return new Response('TODO update');
    }

    public function addFormAction() {
        return new Response('TODO add form');
    }

    public function addAction() {
        return new Response('TODO add');
    }

}