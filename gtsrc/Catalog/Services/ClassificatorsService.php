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
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;

class ClassificatorsService
{
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
        return $this->catalogDao->loadLikeClassificators($filter->getLikeCode(), $filter->getLikeName(), $filter->getGroupCode(), $filter->getLimit());
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
            $c->setAssignedValue($cl->getValue());
        }

        return $classificators;
    }

    /**
     * @param string $file
     * @param string $languageCode
     * @throws CatalogErrorException
     * @throws CatalogValidateException
     * @return int
     */
    public function importClassificators ( $file, string $languageCode ) {
        $language = $this->languageDao->getLanguage ( $languageCode );

        if ( empty($language)) {
            throw new CatalogErrorException('Failed to load language by code '.$languageCode );
        }


        $f = fopen($file, 'r');
        try {

            $header = fgetcsv($f);

            if (count($header) != 3) {
                throw new CatalogValidateException('Paduotame faile yra ' . count($header) . ' stulpelių, o turi būti 3');
            }

            // gal reiktų perkelti šitą gabalą į kitą servisą
            $line = fgetcsv($f);

            /** @var ClassificatorLanguage[] $cls */
            $cls = [];

            /** @var Classificator[] $cs */
            $cs = [];


            while ($line != null) {
                list($code, $name, $group) = $line;
                $cl = ClassificatorLanguage::createLangClassificator($code, $name, $group, $language);
                $cls[] = $cl;
                $cs[] = $cl->getClassificator();
                $line = fgetcsv($f);
            }


            // lets import classificators first


            $this->catalogDao->importClassificators($cs);
            return $this->catalogDao->importClassificatorsLangs($cls);
        } catch ( DBALException $e ) {
            throw new CatalogErrorException( $e->getMessage() );
        } finally {
            fclose($f);
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
        $languages = $this->languageDao->getLanguagesList(0,10);
        return $languages;
    }
}