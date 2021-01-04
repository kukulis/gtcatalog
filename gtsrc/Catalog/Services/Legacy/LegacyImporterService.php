<?php
namespace Gt\Catalog\Services\Legacy;

use Catalog\B2b\Common\Data\Legacy\Catalog\KatalogasPreke;
use Doctrine\DBAL\DBALException;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Services\PicturesService;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use \Exception;

class LegacyImporterService
{
    const STEP=100;

    const LANGUAGES_MAP = [ //TODO galima iš db nuskaityti
      'lit-LT' => 'lt',
      'eng-GB' => 'en',
      'eng-US' => 'en',
      'lav-LV' => 'lv',
      'pol-PL' => 'pl',
      'swe-SE' => 'se',
      'rus-RU' => 'ru',
    ];

    /** @var Logger */
    private $logger;

    /** @var KatalogasClient */
    private $katalogasClient;

    /** @var TmpDao */
    private $tmpDao;

    /** @var PicturesService */
    private $picturesService;

    /**
     * LegacyImporterService constructor.
     * @param LoggerInterface $logger
     * @param KatalogasClient $katalogasClient
     * @param TmpDao $tmpDao
     * @param PicturesService $picturesService
     */
    public function __construct(LoggerInterface $logger,
                                KatalogasClient $katalogasClient,
                                TmpDao $tmpDao,
                                PicturesService $picturesService
)
    {
        $this->logger = $logger;
        $this->katalogasClient = $katalogasClient;
        $this->tmpDao = $tmpDao;
        $this->picturesService = $picturesService;
    }


    public function importToTmp ($katalogasUrl, $localeCode) {
        $this->logger->debug('Importuojam kalbą '.$localeCode);
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

        /** @var TmpProductCategory[] $tmpCategories */
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

//            $this->logger->debug('Dumpinam klasifikatorius' );
//            var_dump ( $tmpClassificatorsPart );
           $tmpClassificatorsPart = array_filter( $tmpClassificatorsPart, function (TmpClassificator  $c ){ return !empty($c->classificator_code); });
//           $this->logger->debug('Dumpinam klasifikatorius po filtravimo' );
//           var_dump ( $tmpClassificatorsPart );

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

    /**
     * @param $url
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     * @throws CatalogErrorException
     */
    public function downloadPictures($url) {
        $pics = $this->tmpDao->getAllUnuploadedTmpPictures();
        $count = 0;
        for ( $i=0; $i <= count($pics); $i+= self::STEP ) {
            $this->logger->debug ( 'from '.$i.' (of '.count($pics). ') and url '.$url );
            $part = array_slice($pics, $i, self::STEP);
            $count += $this->downloadPartPictures($url, $part);
        }

        return $count;
    }

    /**
     * @param $url string
     * @param $part TmpProductPicture[]
     * @return int
     * @throws CatalogErrorException
     */
    private function downloadPartPictures($url, $part) {
        $count = 0 ;
        $picturesReferences = [];
        foreach ($part as $tmpPicture ) {
            if ( empty($tmpPicture->is_downloaded)) {
                try {
                    $pictureUrl = $url . $tmpPicture->url;
                    $content = file_get_contents($pictureUrl);

                    $dir = $this->picturesService->calculatePictureFullDir($tmpPicture->picture_id);
                    if (!file_exists($dir)) {
                        @mkdir($dir, 0775, true);
                    }
                    $path = $this->picturesService->calculatePictureFullPath($tmpPicture->picture_id, $tmpPicture->name);
                    file_put_contents($path, $content);
                    $tmpPicture->is_downloaded = 1;
                    $count += 1;
                    $picturesReferences[] = $tmpPicture->legacy_id;
                } catch (Exception $e) {
                    $this->logger->error('Klaida siunčiantis paveikslėlį '.$tmpPicture->legacy_id.' Prekei'.$tmpPicture->sku.' : '.$e->getMessage());
                }
            }
        }
        try {
            $this->tmpDao->updateDownloaded($picturesReferences);
        } catch (DBALException $e ) {
            throw new CatalogErrorException($e->getMessage());
        }
        return $count;
    }
}