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
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Repository\BrandsRepository;
use Gt\Catalog\Repository\ProductsRepository;
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

    public function loadBrand ( $id ) {
        /** @var BrandsRepository $brandsRepository */
        $brandsRepository = $this->entityManager->getRepository(Brand::class );

        /** @var Brand $brand */
        $brand = $brandsRepository->find($id);
        return $brand;
    }

    /**
     * @param string $brandName
     * @return int
     */
    public function getProductsCount($brandName) {
        /** @var ProductsRepository $productsRepository */
        $productsRepository = $this->entityManager->getRepository(Product::class );
        $count = $productsRepository->getProductsCountByBrand($brandName);
        return $count;
    }

    /**
     * @param Brand $brand
     * @param string $newBrandName
     * @return int
     */
    public function storeBrand ( Brand $brand, $newBrandName ) {
        // validate newBrandName for an existing other brand than the given
        /** @var BrandsRepository $brandRepository */
        $brandRepository = $this->entityManager->getRepository(Brand::class);
        $otherBrands  = $brandRepository->findOtherBrands ( $newBrandName, $brand->getId());
        if ( count($otherBrands) > 0 ) {
            throw new CatalogValidateException('There is other brand id=['.$otherBrands[0]->getId().'] with name '.$newBrandName);
        }

        $oldName = $brand->getBrand();
        $brand->setBrand($newBrandName);

        if ( $oldName == $newBrandName ) {
            throw new CatalogValidateException('You didn\'t change anything' );
        }

        $this->entityManager->persist($brand);

        // updating
        /** @var ProductsRepository $productsRepository */
        $productsRepository = $this->entityManager->getRepository(Product::class);
        $rez = $productsRepository->updateBrands($oldName, $newBrandName);
        $this->entityManager->flush();

        return $rez;
    }
}