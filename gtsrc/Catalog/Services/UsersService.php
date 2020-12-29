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

    public function getFilteredUsers(IUsersFilter $filter) {
        /** @var UserRepository $repository */
        $repository = $this->entityManager->getRepository(User::class);
        $users = $repository->findByFilter($filter);
        return $users;
    }

}