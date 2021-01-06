<?php
/**
 * UsersController.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-28
 * Time: 13:25
 */

namespace Gt\Catalog\Controller;

use Gt\Catalog\Entity\User;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Form\UserAddFormType;
use Gt\Catalog\Form\UserEditFormType;
use Gt\Catalog\Form\UsersFilterFormType;
use Gt\Catalog\Services\UsersService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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

        $user = $usersService->getUser($id);
        if ( $user == null ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => 'There is no user with id',
            ]);
        }

        try {
            /** @var User $currentUser */
            $currentUser = $this->getUser();
            if ( !$currentUser->isAdmin() && $currentUser->getId() != $user->getId() ) {
                throw new CatalogValidateException('Only admin may edit other users' );
            }

            $userFormType = new UserEditFormType();
            $userFormType->setUser($user);
            $userFormType->setEditorAdmin($currentUser->isAdmin());
            $userFormType->setEnabled($user->isEnabled());
            $userFormType->setRolesstr($user->getRolesStr());

            $form = $this->createForm(UserEditFormType::class, $userFormType );

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ( $currentUser->isAdmin()) {
                    $user->setEnabled($userFormType->getEnabled());

                    $roles = explode ( ',', $userFormType->getRolesstr());
                    $rolesClean = array_map ( 'trim', $roles );
                    $rolesFiltered = array_filter($rolesClean, function($role) {return !empty($role);});
                    $user->setRoles($rolesFiltered);
                }
                $usersService->storeUser($user);

                if (!empty($userFormType->getPassword())) {
                    $usersService->storePassword($user, $userFormType->getPassword(), $userFormType->getPassword2());
                }
                return $this->redirectToRoute('gt.catalog.admin_home');
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

    public function addFormAction(Request  $request, UsersService  $usersService) {

        try {
            $userAddFormType = new UserAddFormType();
            $form = $this->createForm(UserAddFormType::class, $userAddFormType);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $usersService->addUser($userAddFormType->getEmail());

                return $this->redirectToRoute('gt.catalog.users_list');
            }

            return $this->render('@Catalog/users/add.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (CatalogValidateException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
            'error' => $e->getMessage(),
        ]);
}
    }

}