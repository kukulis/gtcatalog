<?php
/**
 * CustomsKeywordsService.php
 * Created by Giedrius Tumelis.
 * Date: 2021-04-07
 * Time: 12:41
 */

namespace Gt\Catalog\Services;


use Doctrine\ORM\EntityManager;
use Gt\Catalog\Data\CustomsKeywordsFilter;
use Gt\Catalog\Entity\CustomsKeyword;
use Gt\Catalog\Repository\CustomsKeywordsRepository;
use Psr\Log\LoggerInterface;

class CustomsKeywordsService
{
    /** @var LoggerInterface */
    private $logger;

    /** @var EntityManager */
    private $entityManager;

    /**
     * CustomsKeywordsService constructor.
     * @param LoggerInterface $logger
     * @param EntityManager $entityManager
     */
    public function __construct(LoggerInterface $logger, EntityManager $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function getKeywords(CustomsKeywordsFilter $filter) {
        /** @var CustomsKeywordsRepository $repository */
        $repository = $this->entityManager->getRepository(CustomsKeyword::class);

        return $repository->getKeywords($filter);
    }


}