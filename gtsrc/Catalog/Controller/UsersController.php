<?php
/**
 * UsersController.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-28
 * Time: 13:25
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Form\UserEditFormType;
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

    public function editAction( Request $request, $id, UsersService  $usersService) {
        // TODO validate user rights, as user may edit himself, and only admin may edit other users
        $user = $usersService->getUser($id);
        if ( $user == null ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => 'There is no user with id',
            ]);
        }

        try {
            $userFormType = new UserEditFormType();
            $userFormType->setUser($user);

            $form = $this->createForm(UserEditFormType::class, $userFormType);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $usersService->storeUser($user);

                if (!empty($userFormType->getPassword())) {
                    $usersService->storePassword($user, $userFormType->getPassword(), $userFormType->getPassword2());
                }
                // TODO validate and update password
            }


            return $this->render('@Catalog/users/edit.html.twig', [
                'id' => $id,
                'form' => $form->createView(),
            ]);
        } catch (CatalogValidateException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function addFormAction() {
        return new Response('TODO add form');
    }

    public function addAction() {
        return new Response('TODO add');
    }

}