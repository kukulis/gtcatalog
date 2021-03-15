<?php
/**
 * BrandsRepository.php
 * Created by Giedrius Tumelis.
 * Date: 2021-03-15
 * Time: 13:05
 */

namespace Gt\Catalog\Repository;


use Doctrine\ORM\EntityRepository;
use Gt\Catalog\Data\IBrandsFilter;
use Gt\Catalog\Entity\Brand;


class BrandsRepository extends EntityRepository
{
    public function getList(IBrandsFilter $filter) {
        $builder = $this->createQueryBuilder('b');
        $builder->setMaxResults($filter->getLimit());

        if ( !empty($filter->getOffset())) {
            $builder->setFirstResult($filter->getOffset());
        }

        if ( !empty($filter->getLikeName())) {
            $builder->andWhere( "b.brand like :likeBrand");
            $builder->setParameter("likeBrand", '%'.$filter->getLikeName().'%' );
        }

        if ( !empty($filter->getStartsLike())) {
            $builder->andWhere( "b.brand like :startsLike");
            $builder->setParameter("startsLike", $filter->getStartsLike().'%' );
        }

        /** @var Brand[] $brands */
        $brands = $builder->getQuery()->getResult();
        return $brands;
    }
}