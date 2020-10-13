<?php

namespace Gt\Catalog\Services;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Gt\Catalog\Dao\CategoryDao;
use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Data\CategoriesFilter;
use Gt\Catalog\Entity\Category;
use Gt\Catalog\Entity\CategoryLanguage;
use Gt\Catalog\Entity\Language;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Psr\Log\LoggerInterface;

class CategoriesService
{
    const PAGE_SIZE = 10;

    const DEFAULT_LANGUAGE_CODE = 'en';

    /** @var LoggerInterface */
    private $logger;

    /** @var CategoryDao */
    private $categoryDao;

    /** @var LanguageDao */
    private $languageDao;

    /**
     * CategoriesService constructor.
     * @param LoggerInterface $logger
     * @param CategoryDao $categoryDao
     */
    public function __construct(LoggerInterface $logger,
                                CategoryDao $categoryDao,
                                LanguageDao $languageDao)
    {
        $this->logger = $logger;
        $this->categoryDao = $categoryDao;
        $this->languageDao = $languageDao;
    }

    /**
     * @param CategoriesFilter $filter
     * @return CategoryLanguage[]
     * @throws \Gt\Catalog\Exception\CatalogErrorException
     */
    public function getCategoriesLanguages(CategoriesFilter $filter)
    {
        $categories = $this->categoryDao->getCategories($filter);
        $codes = array_map([Category::class, 'lambdaGetCode'], $categories);

        $languageCode = self::DEFAULT_LANGUAGE_CODE;
        if ($filter->getLanguage() != null) {
            $languageCode = $filter->getLanguage()->getCode();
        }

        $categoriesLanguages = $this->categoryDao->getCategoriesLanguages($codes, $languageCode);

        /** @var CategoryLanguage[] $clMap */
        $clMap = [];

        foreach ($categoriesLanguages as $cl) {
            $clMap[$cl->getCategory()->getCode()] = $cl;
        }

        /** @var CategoryLanguage[] $clResult */
        $clResult = [];

        $language = $this->languageDao->getLanguage($languageCode);
        foreach ($categories as $c) {
            if (array_key_exists($c->getCode(), $clMap)) {
                $cl = $clMap[$c->getCode()];
            } else {
                $cl = new CategoryLanguage();
                $cl->setCategory($c);
                $cl->setLanguage($language);
            }
            $clResult[] = $cl;
        }
        return $clResult;
    }

    /**
     * @param string $code
     * @param string $languageCode
     * @return CategoryLanguage
     * @throws CatalogErrorException
     * @throws CatalogValidateException
     */
    public function getCategoryLanguage($code, $languageCode)
    {
        $categoryLanguage = null;
        try {
            $categoryLanguage = $this->categoryDao->getCategoryLanguage($code, $languageCode);
        } catch (NoResultException $e ) {
            // ok
            $this->logger->notice( 'No category language found with code '.$code. ' language '.$languageCode);
        } catch ( ORMException $e ) {
            throw new CatalogErrorException( $e->getMessage() );
        }

        if (empty($categoryLanguage)) {
            $category = $this->categoryDao->getCategory($code);
            if (empty ($category)) {
                throw new CatalogValidateException('Can\'t load category by code ' . $code);
            }
            $language = $this->languageDao->getLanguage($languageCode);
            if (empty($language)) {
                throw new CatalogValidateException('Can\'t load language by code ' . $languageCode);
            }

            $categoryLanguage = new CategoryLanguage();
            $categoryLanguage->setCategory($category);
            $categoryLanguage->setLanguage($language);
        }
        return $categoryLanguage;
    }

    /**
     * @param CategoryLanguage $cl
     * @return CategoryLanguage
     * @throws CatalogValidateException
     */
    public function storeCategoryLanguage( CategoryLanguage $cl) {
        $this->assignAssociations($cl->getCategory());
        return $this->categoryDao->storeCategoryLanguage($cl);
    }

    /**
     * @return Language[]
     */
    public function getAllLanguages() {
        $languages = $this->languageDao->getLanguagesList(0,10);
        return $languages;
    }

    /**
     * @param Category $c
     * @return Category
     * @throws CatalogValidateException
     */
    public function assignAssociations(Category  $c) {
        if ( !empty( $c->getParentCode() ) ) {
            $parentCategory = $this->categoryDao->getCategory($c->getParentCode());
            if ( $parentCategory == null ) {
                throw new CatalogValidateException('There is no category with code '.$c->getParentCode() );
            }
            $c->setParent($parentCategory);
        }
        return $c;
    }
}