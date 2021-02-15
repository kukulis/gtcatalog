<?php
/**
 * RemoveDuplicatePicturesService.php
 * Created by Giedrius Tumelis.
 * Date: 2021-02-15
 * Time: 10:39
 */

namespace Gt\Catalog\Services;


use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Repository\ProductsRepository;
use Psr\Log\LoggerInterface;

class RemoveDuplicatePicturesService
{
    /** @var LoggerInterface */
    private $logger;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PicturesService */
    private $picturesService;

    /**
     * RemoveDuplicatePicturesService constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        PicturesService $picturesService )
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->picturesService = $picturesService;
    }

    /**
     * @return int
     * @throws \Gt\Catalog\Exception\CatalogErrorException
     */
    public function removeDuplicates() {
        /** @var ProductsRepository $productsRepository */
        $productsRepository = $this->entityManager->getRepository(Product::class );

        $skus = $productsRepository->getAllSkus();

        $this->logger->debug('Extracted '.count($skus).' skus' );
        $skus3 = array_slice($skus,0, 3 );
        $this->logger->debug('First 3 '.join ( ',', $skus3));

        $count = 0;
        $reviewedCount = 0;
        // 1) go throuh all products
        foreach ( $skus as $sku ) {
            // 2) load pictures
            $productPictures = $this->picturesService->getProductPictures($sku);

            // 3) search for duplicates
            $picturesSet = [];
            foreach ($productPictures as $pp ) {
                $picture = $pp->getPicture();
                $name = $picture->getName();
                if ( array_key_exists($name, $picturesSet)) {
                    // 4) remove duplicates
                    $this->picturesService->unassignPicture($sku, $picture->getId(), false);
                    $count ++;
                }
                else {
                    $picturesSet[$name] = $picture;
                }
            }

            $reviewedCount++;
            if ( $reviewedCount % 1000 == 0 ) {
                if ( $count > 0 ) {
                    $this->entityManager->flush();
                }
                $this->entityManager->clear();
                $this->logger->notice('Reviewed products count '.$reviewedCount .' duplicates removed '.$count);
            }
        }
        $this->entityManager->flush();
        return $count;
    }
}