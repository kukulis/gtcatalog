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

    /**
     * ProductsLogService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getList(ProductLogFilter $productLogFilter ): array
    {
        /** @var ProductLogRepository $productLogRepository */
        $productLogRepository = $this->entityManager->getRepository(ProductLog::class );
        return $productLogRepository->getList($productLogFilter);
    }
}