<?php
/**
 * ImportCustomsNumbersService.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-18
 * Time: 13:37
 */

namespace Gt\Catalog\Services;


use Gt\Catalog\Dao\CustomsNumberDao;
use Gt\Catalog\Entity\CustomsNumber;
use Gt\Catalog\Utils\CsvUtils;
use Psr\Log\LoggerInterface;

class ImportCustomsNumbersService
{
    const STEP = 100;

    /** @var LoggerInterface */
    private $logger;

    /** @var CustomsNumberDao */
    private $customsNumberDao;

    /**
     * ImportCustomsNumbersService constructor.
     * @param LoggerInterface $logger
     * @param CustomsNumberDao $customsNumberDao
     */
    public function __construct(LoggerInterface $logger, CustomsNumberDao $customsNumberDao)
    {
        $this->logger = $logger;
        $this->customsNumberDao = $customsNumberDao;
    }


    public function importFromCsv($file) {
        $customsNumbers = $this->readCsv($file);
        $this->logger->debug('Loaded '.count($customsNumbers).' objects' );
        $count = $this->importCustomsNumbers( $customsNumbers );
        return $count;
    }

    private function readCsv ( $file ) {
        $f = fopen($file, 'r');

        $customsNumbers=[];

        $header = fgetcsv($f);
        $headMap = array_flip($header);

        $line=fgetcsv($f);
        while ( $line != null ) {
            $mappedLine = CsvUtils::arrayToAssoc($headMap, $line );
            $customsNumberObj = self::mapToCustomsNumber($mappedLine);
            $customsNumbers[] = $customsNumberObj;
            $line=fgetcsv($f);
        }
        fclose($f);

        return $customsNumbers;
    }

    /**
     * @param CustomsNumber[] $customsNumbers
     * @return int
     */
    public function importCustomsNumbers( $customsNumbers ) {
        $count = 0;

        for ( $i=0; $i < count($customsNumbers); $i += self::STEP ) {
            $this->logger->debug ( 'Importing from '.$i );
            $part = array_slice($customsNumbers, $i, self::STEP );
            $count += $this->customsNumberDao->importCustomNumers($part);
        }

        return $count;
    }

    /**
     * @param string[] $lineMap
     * @return CustomsNumber
     */
    public static function mapToCustomsNumber($lineMap) {
        $customsNumber = new CustomsNumber();
        $customsNumber->setSortingCode($lineMap['SORTING CODE']);
        $customsNumber->setOfficialCode($lineMap['OFFICIAL CODE']);
        $customsNumber->setDescription($lineMap['EN']);
        return $customsNumber;
    }
}