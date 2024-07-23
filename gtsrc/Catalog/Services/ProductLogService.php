<?php

namespace Gt\Catalog\Services;

use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Data\ProductLogFilter;
use Gt\Catalog\Entity\ProductLog;
use Gt\Catalog\Repository\ProductLogRepository;
use Psr\Log\LoggerInterface;

class ProductLogService
{
    /** @var LoggerInterface */
    private $logger;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * BrandsService constructor.
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function getList(ProductLogFilter $productLogFilter ) {
        /** @var ProductLogRepository $productLogRepository */
        $productLogRepository = $this->entityManager->getRepository(ProductLog::class );
        return $productLogRepository->getList($productLogFilter);
    }
}