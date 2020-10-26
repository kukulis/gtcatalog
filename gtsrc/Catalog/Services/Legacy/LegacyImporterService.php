<?php
/**
 * LegacyImporterService.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-23
 * Time: 08:28
 */

namespace Gt\Catalog\Services\Legacy;


use Gt\Catalog\Exception\CatalogErrorException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Sketis\B2b\Common\Data\Catalog\KatalogasPreke;

class LegacyImporterService
{
    const STEP=100;

    const LANGUAGES_MAP = [
      'lit-LT' => 'lt',
      'eng-GB' => 'en',
      'eng-US' => 'en',
      'lav-LV' => 'lv',
      'pol-PL' => 'pl',
      'swe-SE' => 'se',
    ];

    /** @var Logger */
    private $logger;

    /** @var KatalogasClient */
    private $katalogasClient;

    /** @var TmpDao */
    private $tmpDao;

    /**
     * LegacyImporterService constructor.
     * @param LoggerInterface $logger
     * @param KatalogasClient $katalogasClient
     * @param TmpDao $tmpDao
     */
    public function __construct(LoggerInterface $logger,
                                KatalogasClient $katalogasClient,
                                TmpDao $tmpDao)
    {
        $this->logger = $logger;
        $this->katalogasClient = $katalogasClient;
        $this->tmpDao = $tmpDao;
    }


    public function importToTmp ($katalogasUrl, $localeCode) {
        $this->katalogasClient->setKatalogasRestBaseUrl($katalogasUrl.'/api/ezp/v2');
        $this->katalogasClient->setKatalogasSiteBaseUrl($katalogasUrl);

        // 1) nuskaitom nomnr iš db.
        $skus = $this->tmpDao->getAllSkus();
        $count = 0;
        for ( $i=0; $i < count($skus); $i+= self::STEP ) {
            $this->logger->debug('Nuo '.$i.' ('.count($skus).')' );
            $part = array_slice($skus, $i, self::STEP);
            $this->logger->debug('sku_0='.$part[0]);
            // 2) imam po porciją iš tinklo
            $katalogasPrekes = $this->katalogasClient->getPrekesOnly($part, $localeCode);

            // 3) importuojam dalį
            $count += $this->importPart($katalogasPrekes, $localeCode);
        }

        return $count;
    }

    /**
     * @param KatalogasPreke[] $katalogasPrekes
     * @param $localeCode
     * @return int
     * @throws CatalogErrorException
     */
    public function importPart($katalogasPrekes, $localeCode) {

        if ( array_key_exists($localeCode, self::LANGUAGES_MAP) ) {
            $langCode = self::LANGUAGES_MAP[$localeCode];
        }
        else {
            throw new CatalogErrorException('LegacyImporterService: Nesukonfigūruota lokalė '.$localeCode );
        }

        // 3) sumapinam į savo kitą tempinę lentelę
        /** @var TmpFullProduct[] $tmpProducts */
        $tmpProducts = [];

        /** @var TmpFullProductLanguage[] $tmpProductsLanguages */
        $tmpProductsLanguages = [];

       /** @var TmpClassificator[] $tmpClassificators */
        $tmpClassificators = [];

        /** @var TmpProductCategory $tmpCategories */
        $tmpCategories =[];

        /** @var TmpProductPicture[] $tmpProductsPictures */
        $tmpProductsPictures = [];


        $now = date('Y-m-d H:i:s');

        // mapinam
        foreach ($katalogasPrekes as $p ) {

            $kp = KatalogasPreke::convert($p);

           $tmpProduct = TmpProductMapper::mapProduct($kp, $now);
           $tmpProductLanguage = TmpProductMapper::mapProductLanguage($kp, $langCode );
           $tmpClassificatorsPart = TmpProductMapper::mapClassificators($kp, $langCode);

           $tmpClassificatorsPart = array_filter( $tmpClassificatorsPart, function (TmpClassificator  $c ){ return !empty($c->classificator_code); });
           $tmpCategoriesPart = TmpProductMapper::mapProductCategories($kp, $langCode);
           $tmpPicturesPart = TmpProductMapper::mapProductPictures($kp);

           $tmpProducts[] = $tmpProduct;
           $tmpProductsLanguages[] = $tmpProductLanguage;
           $tmpClassificators = array_merge($tmpClassificators, $tmpClassificatorsPart );
           $tmpCategories = array_merge($tmpCategories, $tmpCategoriesPart );
           $tmpProductsPictures = array_merge($tmpProductsPictures, $tmpPicturesPart );
        }


        // 4) Įterpiam pas save.
        $count = $this->tmpDao->importProducts($tmpProducts);
        $this->tmpDao->importProductsLanguages($tmpProductsLanguages);
        $this->tmpDao->importClassificators($tmpClassificators);
        $this->tmpDao->importCategories($tmpCategories);
        $this->tmpDao->importPictures($tmpProductsPictures);
        return $count;
    }
}