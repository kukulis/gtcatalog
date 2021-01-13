<?php
/**
 * ImportPicturesJobRepository.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-30
 * Time: 15:27
 */

namespace Gt\Catalog\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gt\Catalog\Data\IPicturesJobsFilter;
use Gt\Catalog\Entity\ImportPicturesJob;

class ImportPicturesJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportPicturesJob::class);
    }

    /**
     * @param IPicturesJobsFilter $filter
     * @return ImportPicturesJob[]
     */
    public function getByFilter(IPicturesJobsFilter $filter) {
        $builder = $this->createQueryBuilder('j');
        $builder->setMaxResults($filter->getLimit());

        if ( !empty($filter->getStatus())) {
            $builder->andWhere('j.status=:status');
            $builder->setParameter('status', $filter->getStatus() );
        }

        /** @var ImportPicturesJob[] $jobs */
        $jobs = $builder->getQuery()->getResult();

        return $jobs;
    }

    public function createNewJob($name, $createdDate) {
        $job = new ImportPicturesJob();
        $job->setName($name);
        $job->setCreatedTime($createdDate);
        $job->setStatus(ImportPicturesJob::STATUS_NONE );
        $this->_em->persist($job);
        $this->_em->flush();
        return $job;
    }

    /**
     * @param string $status
     * @param int $limit
     * @return ImportPicturesJob[]
     */
    public function getJobsInStatus($status, $limit) {
        $class = ImportPicturesJob::class;
        $dql = /** @lang DQL */ "SELECT j FROM $class j WHERE j.status=:status";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('status', $status );
        $query->setMaxResults($limit);

        /** @var ImportPicturesJob[] $jobs */
        $jobs = $query->getResult();

        return $jobs;
    }

    /**
     * @param ImportPicturesJob[] $jobs
     * @param string $status
     */
    public function setStatuses ( $jobs, $status ) {
        foreach ($jobs as $job ) {
            $job->setStatus($status);
            $this->_em->persist($job);
        }

        $this->_em->flush();
    }

}