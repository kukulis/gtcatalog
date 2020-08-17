<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.8
 * Time: 07.31
 */

namespace Gt\Catalog\Services;


use Gt\Catalog\Dao\CatalogDao;
use Gt\Catalog\Dao\ClassificatorDao;
use Gt\Catalog\Dao\ClassificatorGroupDao;
use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Data\ClassificatorsListFilter;
use Gt\Catalog\Entity\ClassificatorGroup;
use Gt\Catalog\Entity\ClassificatorLanguage;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Form;
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
     * @param string $file
     * @param string $languageCode
     * @throws CatalogErrorException
     * @throws CatalogValidateException
     */
    public function importClassificators ( $file, string $languageCode ) {
//        throw new CatalogErrorException('Not implemented');

        $language = $this->languageDao->getLanguage ( $languageCode );

        if ( empty($language)) {
            throw new CatalogErrorException('Failed to load language by code '.$languageCode );
        }


        try {
            $f = fopen($file, 'r');
            $header = fgetcsv($f);

            if (count($header) != 3) {
                throw new CatalogValidateException('Paduotame faile yra ' . count($header) . ' stulpelių, o turi būti 3');
            }

            // gal reiktų perkelti šitą gabalą į kitą servisą
            $line = fgetcsv($f);

            /** @var ClassificatorLanguage[] $cls */
            $cls = [];

            while ($line != null) {
                list($code, $name, $group ) = $line;
                $cls[] = ClassificatorLanguage::createLangClassificator($code, $name, $group, $language );
                $line = fgetcsv($f);
            }

            $this->catalogDao->importClassificatorsLangs ( $cls );
        } finally {
            fclose($f);
        }
    }

    public function newClassificator(FormInterface $form)
    {
        $data = $form->getData();
        $this->classificatorDao->addClassificator($data);
    }
}