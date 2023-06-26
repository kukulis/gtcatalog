<?php

namespace Gt\Catalog\Services\Rest;

use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Data\SimpleCategoriesFilter;
use Gt\Catalog\Entity\Category;
use Gt\Catalog\Entity\CategoryLanguage;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Services\CategoriesService;
use Gt\Catalog\Utils\CategoriesHelper;
use Gt\Catalog\Utils\CategoriesMapper;
use Psr\Log\LoggerInterface;

use \Catalog\B2b\Common\Data\Catalog\Category as RestCategory;

class CategoriesRestService
{
    /** @var LoggerInterface */
    private $logger;

    /** @var CategoriesService */
    private $categoriesService;

    /** @var LanguageDao */
    private $languageDao;

    /**
     * CategoriesRestService constructor.
     * @param LoggerInterface $logger
     * @param CategoriesService $categoriesService
     * @param LanguageDao $languageDao
     */
    public function __construct(LoggerInterface $logger, CategoriesService $categoriesService, LanguageDao $languageDao)
    {
        $this->logger = $logger;
        $this->categoriesService = $categoriesService;
        $this->languageDao = $languageDao;
    }


    /**
     * @param string $lang
     * @return RestCategory[]
     * @throws CatalogErrorException
     * @throws CatalogValidateException
     */
    public function getRestCategories(string $lang, $offset, $limit) {
        $categoriesFilter = new SimpleCategoriesFilter();
        $categoriesFilter->setLimit($limit);
        $categoriesFilter->setOffset($offset);
        $language = $this->languageDao->getLanguage($lang);
        if ( $language == null ) {
            throw new CatalogValidateException('Wrong language code '.$lang );
        }
        $categoriesFilter->setLanguage($language);
        $categories = $this->categoriesService->getCategoriesLanguages($categoriesFilter);
        $restCategories = array_map ( [CategoriesMapper::class, 'mapCategoryLanguageToRestCategory'], $categories);
        return $restCategories;
    }

    /**
     * @return string[]
     */
    public function getCategoriesRoots() {
        $categories = $this->categoriesService->getRootCategories();
//        $restCategories = array_map ( [CategoriesMapper::class, 'mapCategoryToRestCategory'], $categories);
        // lets return only codes
        $codes = array_map (function(Category $c){return $c->getCode();}, $categories);
        return $codes;
    }

    /**
     * @param string $categoryCode
     * @param string $lang
     * @return RestCategory[]
     * @throws CatalogValidateException
     * @throws CatalogErrorException
     */
    public function getCategoriesTree($categoryCode, $lang ) {
        $l = $this->languageDao->getLanguage($lang);
        if ( $l == null ) {
            throw new CatalogValidateException('Wrong language code ['.$lang.']');
        }

        // load the given category
        $c = $this->categoriesService->getCategory($categoryCode);

        if ( $c == null ) {
            throw new CatalogValidateException('Wrong category code ['.$categoryCode.']' );
        }

        /** @var Category[] $categoriesMap */
        $categoriesMap = [];
        $categoriesMap[$c->getCode()] = $c;

        /** @var Category[] $lastLevel */
        $lastLevel =[$c];

        // 1) load categories tree
        while ( count($lastLevel) > 0 ) {
            $lastLevelCodes = array_map(function (Category $c) {
                return $c->getCode();
            }, $lastLevel);

            $newLevel = $this->categoriesService->getCategoriesByParentCodes($lastLevelCodes);
            // put to map
            foreach ($newLevel as $c) {
                $categoriesMap[$c->getCode()] = $c;
            }
            $lastLevel = $newLevel;
        }

        // 2) load categories langs
        $categoriesLangs = $this->categoriesService->getCategoriesLanguagesByCodes(array_keys($categoriesMap), $lang);

        // 3) build missing categories langs
        // put to map to find by code
        /** @var CategoryLanguage[] $clMap */
        $clMap = [];
        foreach ($categoriesLangs as $cl ) {
            $clMap[$cl->getCode()] = $cl;
        }
        // missing categories langs will be created at next step

        // 4) build catagories languages tree
        foreach ( $categoriesMap as $code => $category ) {
            // A) find category language
            // if not found, create empty
            if ( !array_key_exists($code, $clMap)) {
                $cl = new CategoryLanguage();
                $cl->setCategory($category);
                $cl->setName($code);
                $clMap[$code] = $cl;
            }
        }

        // 5) build rest categories tree
        /** @var RestCategory[] $restCategoriesMap */
        $restCategoriesMap=[];
        foreach ($categoriesMap as $code => $category ) {
            $cl = $clMap[$code];

            // A) transform to rest category
            $restCategory = CategoriesMapper::mapCategoryLanguageToRestCategory($cl);

            $parent = $c->getParent();
            if ( $parent == null ) {
                // I am root
                $restCategory->path = '/'.$code;
            }
            else {
                if ( $categoryCode == $code ) {
                    $meWithAncestors = $this->categoriesService->getWithAncestors($code);
                    $restCategory->path = CategoriesHelper::constructPathByAncestors( $meWithAncestors );
                }
                else {
                    // B) calculate path by parent
                    $parentCode = $parent->getCode();
                    if (!array_key_exists($parentCode, $restCategoriesMap)) {
                        throw new CatalogErrorException('Uncreated rest category [' . $parentCode . ']');
                    }
                    $restParent = $restCategoriesMap[$parentCode]; // jei null, tai vadinasi kažkokia programinė klaida?

                    // C) put myself into parent children list
                    $restCategory->path = $restParent->path . '/' . $code;
                    $restParent->children[] = $code;
                }
            }
            $restCategoriesMap[$code] = $restCategory;
        }
        return array_values($restCategoriesMap);
    }



    /**
     * @param string $categoryCode
     * @param string $lang
     * @return RestCategory
     * @throws CatalogValidateException
     * @throws \Gt\Catalog\Exception\CatalogErrorException
     */
    public function getCategoryLang($categoryCode, $lang) {
        $categoryLanguage = $this->categoriesService->getCategoryLanguage($categoryCode, $lang);
        $restCategory = CategoriesMapper::mapCategoryLanguageToRestCategory($categoryLanguage);
        $meWithAncestors = $this->categoriesService->getWithAncestors($categoryCode);
        $restCategory->path = CategoriesHelper::constructPathByAncestors($meWithAncestors);
        return $restCategory;
    }


}