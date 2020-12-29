<?php
/**
 * UsersService.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-28
 * Time: 13:58
 */

namespace Gt\Catalog\Services;


use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Data\IUsersFilter;
use Gt\Catalog\Entity\User;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Repository\UserRepository;
use Psr\Log\LoggerInterface;

class UsersService
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * UsersService constructor.
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    /**
     * @param IUsersFilter $filter
     * @return User[]
     */
    public function getFilteredUsers(IUsersFilter $filter) {
        /** @var UserRepository $repository */
        $repository = $this->entityManager->getRepository(User::class);
        $users = $repository->findByFilter($filter);
        return $users;
    }

    /**
     * @param $id
     * @return User|null
     */
    public function getUser($id) {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->find($id);
        return $user;
    }

    public function storeUser (User $user) {
        $this->entityManager->persist($user);
    }

    public function storePassword ( $user, $password, $password2 ) {
        if ( strcmp( $password, $password2) !== 0 ) {
            throw new CatalogValidateException('Passwords doesn\'t match each other' );
        }

        if ( strlen($password) < 12 ) {
            throw new CatalogValidateException('Password lenght must be not less than 12 symbols' );
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userRepository->upgradePassword($user, $encryptedPassword);
    }

}