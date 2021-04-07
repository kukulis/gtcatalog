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
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Repository\CustomsKeywordsRepository;
use Psr\Log\LoggerInterface;

class CustomsKeywordsService
{
    const STEP = 100;

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


    /**
     * @param $path
     * @param $fileName
     * @return int
     * @throws CatalogValidateException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function importKeywordsFromCsvFile($path, $fileName) {
        // parse file to entities array
        /** @var CustomsKeyword[] $customsKeywords */
        $customsKeywords = [];
        $f = fopen ( $path, 'r' );
        $head = fgetcsv($f);
        $line = fgetcsv($f);
        while ( !empty($line) && count($line) > 0 ) {
            if ( count($line) < 2 ) {
                throw new CatalogValidateException('File ['.$fileName.'] lines must contain two columns: customs_code and a keyword');
            }
            list($customsCode, $keywordsStr)= $line;
            $keywords = explode(',', $keywordsStr );
            foreach ($keywords as $keyword ) {
                if ( empty($keyword)) {
                    continue;
                }
                $customsKeyword = new CustomsKeyword();
                $customsKeyword->setCustomsCode($customsCode);
                $customsKeyword->setKeyword(strtolower($keyword));
                $customsKeywords[] = $customsKeyword;
            }
            $line = fgetcsv($f);
        }
        fclose ( $f );

        $this->logger->debug('Loaded [' . count($customsKeywords ) . '] customs keywords' );

        // import entities array through repository class
        /** @var CustomsKeywordsRepository $repository */
        $repository = $this->entityManager->getRepository(CustomsKeyword::class );
        $importedCount = 0;
        for ( $i = 0; $i < count($customsKeywords); $i+= self::STEP ) {
            $part = array_slice ( $customsKeywords, $i, self::STEP );
            $importedCount += $repository->importKeywords($part);
        }

        return $importedCount;
    }

    /**
     * @param int $id
     * @throws CatalogValidateException
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteKeyword($id) {
        /** @var CustomsKeywordsRepository $repository */
        $repository = $this->entityManager->getRepository(CustomsKeyword::class );

        $customsKeyword = $repository->find($id);
        if ( empty($customsKeyword)) {
            throw new CatalogValidateException('There is no keyword with id ['.$id.']');
        }

        $this->entityManager->remove($customsKeyword);
        $this->entityManager->flush();
    }
}