<?php
/**
 * BrandsService.php
 * Created by Giedrius Tumelis.
 * Date: 2021-03-15
 * Time: 13:12
 */

namespace Gt\Catalog\Services;


use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Data\IBrandsFilter;
use Gt\Catalog\Entity\Brand;
use Gt\Catalog\Repository\BrandsRepository;
use Psr\Log\LoggerInterface;

class BrandsService
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

    public function getList(IBrandsFilter $brandsFilter ) {
        /** @var BrandsRepository $brandsRepository */
        $brandsRepository = $this->entityManager->getRepository(Brand::class );
        return $brandsRepository->getList($brandsFilter);
    }
}