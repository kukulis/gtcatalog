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
}