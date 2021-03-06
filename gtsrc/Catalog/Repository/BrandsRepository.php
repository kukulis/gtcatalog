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

        $builder->orderBy('b.brand' );

        /** @var Brand[] $brands */
        $brands = $builder->getQuery()->getResult();
        return $brands;
    }

    /**
     * @param string $brandName
     * @param int $notId
     * @return Brand[]
     */
    public function findOtherBrands($brandName, $notId) {
        $class = Brand::class;
        $dql = /** @lang DQL */ "SELECT b FROM $class b WHERE b.brand=:brandName AND b.id != :notId";
        $query = $this->_em->createQuery($dql);

        $query->setParameter('brandName', $brandName);
        $query->setParameter('notId', $notId );

        /** @var Brand[] $brands */
        $brands = $query->getResult();

        return $brands;
    }

    /**
     * @param $brandsNames
     * @return Brand[]
     */
    public function loadBrands($brandsNames) {
        $class = Brand::class;
        $dql = /** @lang DQL  */ "SELECT b FROM $class b WHERE b.brand in (:brands)";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('brands', $brandsNames );
        /** @var Brand[] $brands */
        $brands = $query->getResult();
        return $brands;
    }

    /**
     * @param string[] $brandsNames
     * @param int $step
     * @return Brand[]
     */
    public function loadBrandsBatch ( $brandsNames, $step ) {
        /** @var Brand[] $allBrands */
        $allBrands = [];
        for ( $i=0; $i < count($brandsNames); $i+= $step ) {
            $part = array_slice($brandsNames, $i, $step );

            $brands = $this->loadBrands($part);
            $allBrands = array_merge($allBrands, $brands);
        }
        return $allBrands;
    }

}