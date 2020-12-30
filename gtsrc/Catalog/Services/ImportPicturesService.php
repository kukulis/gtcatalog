<?php
/**
 * ImportPicturesService.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-30
 * Time: 15:11
 */

namespace Gt\Catalog\Services;


use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Data\IPicturesJobsFilter;
use Gt\Catalog\Entity\ImportPicturesJob;
use Gt\Catalog\Repository\ImportPicturesJobRepository;
use Psr\Log\LoggerInterface;

class ImportPicturesService
{
    /** @var LoggerInterface */
    private $logger;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * ImportPicturesService constructor.
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    /**
     * @param IPicturesJobsFilter $filter
     * @return ImportPicturesJob[]
     */
    public function getJobs( IPicturesJobsFilter $filter ) {
        /** @var ImportPicturesJobRepository $repository */
        $repository = $this->entityManager->getRepository(ImportPicturesJob::class);
        $jobs = $repository->getByFilter($filter);

        return $jobs;
    }
}