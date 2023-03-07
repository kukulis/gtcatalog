<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.8
 * Time: 07.31
 */

namespace Gt\Catalog\Services;


use Doctrine\DBAL\DBALException;
use Gt\Catalog\Dao\CatalogDao;
use Gt\Catalog\Dao\ClassificatorDao;
use Gt\Catalog\Dao\ClassificatorGroupDao;
use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Data\ClassificatorsListFilter;
use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\ClassificatorGroup;
use Gt\Catalog\Entity\ClassificatorLanguage;
use Gt\Catalog\Entity\Language;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Utils\CategoriesHelper;
use Gt\Catalog\Utils\CsvUtils;
use Psr\Log\LoggerInterface;

class ClassificatorsService
{
    const STEP = 100;

    /** @var LoggerInterface */
    private $logger;

    /** @var ClassificatorGroupDao */
    private $classificatorGroupDao;

    /** @var ClassificatorDao */
    private $classificatorDao;

    /** @var CatalogDao */
    private $catalogDao;

    /** @var LanguageDao */
    private $languageDao;

    /**
     * ClassificatorsService constructor.
     * @param LoggerInterface $logger
     * @param ClassificatorGroupDao $classificatorGroupDao
     */
    public function __construct(LoggerInterface $logger,
                                ClassificatorGroupDao $classificatorGroupDao,
                                ClassificatorDao  $classificatorDao,
                                CatalogDao $catalogDao,
                                LanguageDao $languageDao)
    {
        $this->logger = $logger;
        $this->classificatorGroupDao = $classificatorGroupDao;
        $this->classificatorDao = $classificatorDao;
        $this->catalogDao = $catalogDao;
        $this->languageDao = $languageDao;
    }

    /**
     * @return ClassificatorGroup[]
     */
    public function getAllGroups() {
        return $this->classificatorGroupDao->getClassificatorGroups(0, 50 );
    }

    /**
     * @param ClassificatorsListFilter $filter
     * @return \Gt\Catalog\Entity\Classificator[]
     */
    public function searchClassificators (ClassificatorsListFilter $filter) {
        return $this->catalogDao->loadLikeClassificators($filter->getLikeCode(), $filter->getLikeName(), $filter->getGroupCode(), $filter->getLanguageCode(), $filter->getLimit());
    }

    /**
     * @param Classificator[] $classificators
     * @param string $languageCode
     * @return Classificator[] $classificators
     */
    public function assignValues ( $classificators, $languageCode ) {
        $codes = array_map( [Classificator::class, 'lambdaGetCode'], $classificators );

        /** @var ClassificatorLanguage[] $classificatorsLanguages */
        $classificatorsLanguages = $this->classificatorDao->loadClassificatorsLanguages ( $codes, $languageCode);

        /** @var ClassificatorLanguage[] $map */
        $map = [];
        foreach ( $classificatorsLanguages as $cl ) {
            $map[$cl->getClassificator()->getCode()] = $cl;
        }

        foreach ($classificators as $c ) {
            if ( !array_key_exists($c->getCode(), $map)) {
                continue;
            }
            $cl =  $map[$c->getCode()];
            $c->setAssignedValue($cl->getName());
        }

        return $classificators;
    }

    /**
     * @param string $file
     * @throws CatalogErrorException
     * @throws CatalogValidateException
     * @return int
     */
    public function importClassificators ( $file ) {
        $f = fopen($file, 'r');
        try {
            $header = fgetcsv($f);
            $this->validateHead($header);
            $headMap = array_flip ( $header );

            /** @var ClassificatorLanguage[] $cls */
            $cls = [];

            /** @var Classificator[] $cs */
            $cs = [];

            // gal reiktų perkelti šitą gabalą į kitą servisą
            $l = fgetcsv($f);
            while ($l != null) {

                $l = array_map ( 'trim', $l );
                $line = CsvUtils::arrayToAssoc($headMap, $l);

                $cl = self::mapCsvLineToClassificatorLanguage($line);
                if ( $cl->getName() != null ) {
                    $cls[] = $cl;
                }
                $cs[] = $cl->getClassificator();
                $l = fgetcsv($f);
            }

            // ar reikia "tik update" vėliavėlės ir jos handlinimo čia?
            $count = $this->importClassificatorsDatas($cs, $headMap);
            $countl = $this->importClassificatorsLangsDatas($cls, $headMap);

            return max($count, $countl);

        } catch ( DBALException $e ) {
            throw new CatalogErrorException( $e->getMessage() );
        } finally {
            fclose($f);
        }
    }

    /**
     * @param Classificator[] $cs
     * @param array $givenFieldsSet
     * @return int
     * @throws DBALException
     */
    public function importClassificatorsDatas ($cs, $givenFieldsSet) {
        $count = 0;
        for ( $i=0; $i < count($cs); $i += self::STEP ) {
            $part = array_slice($cs, $i, self::STEP);
            $count += $this->catalogDao->importClassificators($part, $givenFieldsSet);
        }
        return $count;
    }

    /**
     * @param ClassificatorLanguage[] $cls
     * @param array $givenFieldsSet
     * @return int
     * @throws DBALException
     */
    public function importClassificatorsLangsDatas ( $cls, $givenFieldsSet ) {
        $count = 0;
        for ( $i=0; $i < count($cls); $i += self::STEP ) {
            $part = array_slice($cls, $i, self::STEP);
            return $this->catalogDao->importClassificatorsLangs($part, $givenFieldsSet);
        }
        return $count;
    }

    /**
     * @param $line
     * @param Language|null $language
     * @return ClassificatorLanguage
     * @throws CatalogValidateException
     */
    public static function mapCsvLineToClassificatorLanguage ($line, Language $language=null) {
        $code = strtolower($line['code']);
        $group = $line['classificator_group'];

        $customsCode = null;
        if ( array_key_exists('customs_code', $line )) {
            $customsCode = $line['customs_code'];
        }

        $languageCode = $line['language'];

        $name = null;
        if ( array_key_exists('name', $line )) {
            $name = $line['name'];
        }

        if ( !CategoriesHelper::validateClassificatorCode($code) ) {
            throw new CatalogValidateException('Invalid classificator  code:['.$code.']');
        }

        $classificator = Classificator::createClassificator($code, $group);

        if ( $customsCode != null && !empty($customsCode) ) {
            if ( !CategoriesHelper::validateCustomsCode($customsCode) ) {
                throw new CatalogValidateException('Wrong customs code: '.$customsCode );
            }
            $classificator->setCustomsCode($customsCode);
        }

        if ( null == $language  ) { // if not given in parameters, create it dynamically
            $language = new Language();
            $language->setCode($languageCode);
        }

        $classificatorLang = new ClassificatorLanguage();
        $classificatorLang->setLanguage($language);
        if ( $name != null ) {
            $classificatorLang->setName($name);
        }
        $classificatorLang->setClassificator($classificator);
        return $classificatorLang;
    }

    /**
     * @param array $head
     * @throws CatalogValidateException
     */
    public function validateHead ( $head ) {
        $classificatorAndLanguageFields = array_merge(Classificator::ALLOWED_FIELDS, ClassificatorLanguage::ALLOWED_FIELDS);
        $nonValidFields = array_diff ( $head, $classificatorAndLanguageFields );

        if ( count($nonValidFields) > 0 ) {
            throw new CatalogValidateException('Non valid fields:'.join(',', $nonValidFields));
        }

        $requiredFields = ['code', 'classificator_group', 'language' ];

        $missingFields = array_diff($requiredFields, $head );
        if ( count($missingFields) > 0 ) {
            throw new CatalogValidateException('Missing fields:'.join(',', $missingFields));
        }
    }

    public function storeClassificator(Classificator $classificator) {
        $this->classificatorDao->storeClassificator($classificator);
    }

    /**
     * @param $code
     * @return Classificator
     */
    public function loadClassificator($code) {
        if ( empty($code)) {
            $classificator = new Classificator();
            return $classificator;
        }
        else {
            $classificator = $this->classificatorDao->loadClassificator($code);
            return $classificator;
        }
    }

    /**
     * @param $code
     * @param $languageCode
     * @return ClassificatorLanguage|null
     * @throws CatalogErrorException
     */
    public function loadClassificatorLanguage ( $code, $languageCode ) {
        if ( empty($languageCode) ) {
            return null;
        }
        $cl = $this->classificatorDao->loadClassificatorLanguage ( $code, $languageCode);

        if ( !empty($cl)) {
            return $cl;
        }

        // nepavyko užkrauti, todėl sukurkime
        $classificator = $this->classificatorDao->loadClassificator($code);
        if (empty($classificator)) {
            throw new CatalogErrorException('Nepavyko užkrauti klasifikatoriaus pagal kodą '.$code );
        }

        $language = $this->languageDao->getLanguage($languageCode);
        if ( empty($language)) {
            throw new CatalogErrorException('Nėra kalbos pagal kodą '.$languageCode );
        }

        $classificatorLanguage = new ClassificatorLanguage();
        $classificatorLanguage->setClassificator($classificator);
        $classificatorLanguage->setLanguage( $language );

        return $classificatorLanguage;
    }

    /**
     * @param ClassificatorLanguage $cl
     */
    public function storeClassificatorLanguage(ClassificatorLanguage $cl ) {
        $this->classificatorDao->storeClassificator($cl->getClassificator()); // ar tikrai reikia šito?
        $this->classificatorDao->storeClassificatorLanguage($cl);

    }

    /**
     * @return Language[]
     */
    public function getAllLanguages() {
        $languages = $this->languageDao->getLanguagesList(0,100);
        return $languages;
    }
}