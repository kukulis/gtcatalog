<?php

namespace Gt\Catalog\Services;

use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Data\ProductLogFilter;
use Gt\Catalog\Entity\ProductLog;
use Gt\Catalog\Repository\ProductLogRepository;
use Symfony\Component\Security\Core\Security;

class ProductLogService
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;
    private $security;

    /**
     * BrandsService constructor.
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function getList(ProductLogFilter $productLogFilter ) {
        /** @var ProductLogRepository $productLogRepository */
        $productLogRepository = $this->entityManager->getRepository(ProductLog::class );
        return $productLogRepository->findAll();
    }
}