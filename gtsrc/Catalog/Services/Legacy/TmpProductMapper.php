<?php
namespace Gt\Catalog\Services\Legacy;


use Catalog\B2b\Common\Data\Legacy\Catalog\KatalogasPreke;
use Catalog\B2b\Common\Data\Legacy\Catalog\Nuotrauka;
use Gt\Catalog\Utils\ProductsHelper;
use Gt\Catalog\Utils\PropertiesHelper;

class TmpProductMapper
{
    /**
     * @param KatalogasPreke $kp
     * @param $now
     * @return TmpFullProduct
     */
    public static function mapProduct ( KatalogasPreke $kp, $now ) {
        $tmpProduct = new TmpFullProduct();
        $tmpProduct->sku                            = $kp->nomnr;
        $tmpProduct->last_update                    = $now;
        $tmpProduct->version                        = 1;
        $tmpProduct->parent_sku                     = $kp->parent;
        $tmpProduct->origin_country_code            = '';
        $tmpProduct->color                          = $kp->Atributai->spalva;
        $tmpProduct->for_male                       = ($kp->Atributai->vyrams)?1:0;
        $tmpProduct->for_female                     = ($kp->Atributai->moterims)?1:0;
        $tmpProduct->size                           = $kp->Atributai->dydis;
        $tmpProduct->pack_size                      = $kp->Atributai->pack_size;
        $tmpProduct->pack_amount                    = $kp->Atributai->kiekis;
        $tmpProduct->weight                         = $kp->Atributai->svoris;
        $tmpProduct->length                         = $kp->Atributai->ilgis;
        $tmpProduct->height                         = $kp->Atributai->aukstis;
        $tmpProduct->width                          = $kp->Atributai->plotis;
        $tmpProduct->delivery_time                  = $kp->Atributai->pristatymo_laikas;
        $tmpProduct->info_provider                  = $kp->info_provider;
        $tmpProduct->brand                          = PropertiesHelper::truncate($kp->brandas, 64 );
        $tmpProduct->line                           = PropertiesHelper::truncate($kp->linija, 64 );
        $tmpProduct->vendor                         = null; // blogi duomenys kataloge
        $tmpProduct->manufacturer                   = PropertiesHelper::truncate ($kp->Aprasymas->gamintojas, 64 );
        $tmpProduct->type                           = ProductsHelper::fixCode($kp->Atributai->tipas);
        $tmpProduct->purpose                        = ProductsHelper::fixCode($kp->Atributai->paskirtis);
        $tmpProduct->measure                        = ProductsHelper::fixCode($kp->Atributai->matas);
        $tmpProduct->productgroup                   = ProductsHelper::fixCode($kp->Atributai->prekiu_grupe);
        $tmpProduct->deposit_code                   = $kp->depozito_kodas;
        $tmpProduct->code_from_custom               = $kp->muitines_kodas;
        $tmpProduct->guaranty                       = $kp->Atributai->garantija;
        $tmpProduct->code_from_supplier             = '';
        $tmpProduct->code_from_vendor               = '';
        $tmpProduct->priority                       = $kp->Atributai->priority;
        $tmpProduct->google_product_category_id     = intval($kp->Atributai->google_product_category);

        return $tmpProduct;
    }

    /**
     * @param KatalogasPreke $kp
     * @param $langCode
     * @return TmpFullProductLanguage
     */
    public static function mapProductLanguage ( KatalogasPreke $kp, $langCode ) {
        $tmpProductLanguage = new TmpFullProductLanguage();
        $tmpProductLanguage->sku           = $kp->nomnr;
        $tmpProductLanguage->language      = $langCode;
        $tmpProductLanguage->name          = $kp->Aprasymas->pavadinimas ?? '';
        $tmpProductLanguage->description   = $kp->Aprasymas->aprasymas;
        $tmpProductLanguage->label         = $kp->Aprasymas->etiketes_tekstas;
        $tmpProductLanguage->variant_name  = $kp->Aprasymas->var_name;
        $tmpProductLanguage->info_provider = $kp->Aprasymas->info_provider;
        $tmpProductLanguage->tags          =  is_array($kp->Aprasymas->tagai) ? join ( ',', $kp->Aprasymas->tagai ) : $kp->Aprasymas->tagai;
        $tmpProductLanguage->label_size    = is_string($kp->Aprasymas->etiketes_dydis)? substr( $kp->Aprasymas->etiketes_dydis, 32 ):strval($kp->Aprasymas->etiketes_dydis);
        $tmpProductLanguage->distributor   = $kp->Aprasymas->platintojas;
        $tmpProductLanguage->composition   = $kp->Aprasymas->sudetis;

        return $tmpProductLanguage;
    }

    /**
     * @param KatalogasPreke $kp
     * @param $langCode
     * @return TmpProductCategory[]
     */
    public static function mapProductCategories ( KatalogasPreke  $kp, $langCode ) {
        /** @var TmpProductCategory[] $tmpProductCategories */
        $tmpProductCategories = [];
        if ( is_array($kp->categories)) {
            foreach ($kp->categories as $c) {
                $tmpProductCategory = new TmpProductCategory();
                $tmpProductCategory->category = ProductsHelper::fixCode($c->identifikatorius);
                $tmpProductCategory->parent = ProductsHelper::fixCode($c->parent);
                $tmpProductCategory->sku = $kp->nomnr;
                $tmpProductCategory->language = $langCode;
                $tmpProductCategory->name = $c->title;
                $tmpProductCategory->description = $c->title;

                $tmpProductCategories[] = $tmpProductCategory;
            }
        }
        return $tmpProductCategories;
    }

    /**
     * @param KatalogasPreke $kp
     * @param $langCode
     * @return TmpClassificator[]
     */
    public static function mapClassificators(KatalogasPreke  $kp, $langCode) {
        /** @var TmpClassificator[] $tmpClassificators */
        $tmpClassificators = [];

        // nereikia šitų
//        $brandC = new TmpClassificator();
//        $brandC->group_code = 'brand';
//        $brandC->language_code = $langCode;
//        $brandC->classificator_code = ProductsHelper::fixCode($kp->brandas);
//        $brandC->value = $kp->brandas;
//        $tmpClassificators[] = $brandC;
//
//        $lineC = new TmpClassificator();
//        $lineC->group_code = 'line';
//        $lineC->language_code = $langCode;
//        $lineC->classificator_code = ProductsHelper::fixCode($kp->linija);
//        $lineC->value = $kp->linija;
//        $tmpClassificators[] = $lineC;

        // blogi duomenys kataloge
//        $vendorC = new TmpClassificator();
//        $vendorC->group_code = 'vendor';
//        $vendorC->classificator_code = $kp->Aprasymas->platintojas;
//        $vendorC->value = $kp->Aprasymas->platintojas;
//        $vendorC->language_code = $langCode;
//        $tmpClassificators[] = $vendorC;

//        $manufacturerC = new TmpClassificator();
//        $manufacturerC->language_code = $langCode;
//        $manufacturerC->group_code = 'manufacturer';
//        $manufacturerC->classificator_code = ProductsHelper::fixCode($kp->Atributai->gamintojo_kodas);
//        $manufacturerC->value = $kp->Atributai->gamintojo_kodas;
//        $tmpClassificators[] = $manufacturerC;

        $typeC = new TmpClassificator();
        $typeC->language_code = $langCode;
        $typeC->group_code = 'type';
        $typeC->classificator_code = ProductsHelper::fixCode($kp->Atributai->tipas);
        $typeC->value = $kp->Atributai->tipas_title;
        $tmpClassificators[] = $typeC;

        $purposeC = new TmpClassificator();
        $purposeC->language_code = $langCode;
        $purposeC->group_code = 'purpose';
        $purposeC->classificator_code = ProductsHelper::fixCode($kp->Atributai->paskirtis);
        $purposeC->value = $kp->Atributai->paskirtis_title;
        $tmpClassificators[] = $purposeC;

        $measureC = new TmpClassificator();
        $measureC->language_code = $langCode;
        $measureC->group_code = 'measure';
        $measureC->classificator_code = ProductsHelper::fixCode($kp->Atributai->matas);
        $measureC->value = $kp->Atributai->matas_title??$kp->Atributai->matas;
        $tmpClassificators[] = $measureC;


        $productGroupC = new TmpClassificator();
        $productGroupC->language_code = $langCode;
        $productGroupC->group_code = 'productgroup';
        $productGroupC->classificator_code = ProductsHelper::fixCode($kp->Atributai->prekiu_grupe);
        $productGroupC->value = $kp->Atributai->prekiu_grupe_title;
        $tmpClassificators[] = $productGroupC;

        return $tmpClassificators;
    }

    /**
     * @param KatalogasPreke $kp
     * @return TmpProductPicture[]
     */
    public static function mapProductPictures ( KatalogasPreke $kp ) {
        /** @var TmpProductPicture[] $tmpProductPictures */
        $tmpProductPictures = [];

        $fields = [
            'nuotrauka',
            'nuotrauka2',
            'nuotrauka3',
            'nuotrauka4',
            'nuotrauka5',
            'nuotrauka6',
            'nuotrauka7',
            'nuotrauka8',
            'nuotrauka9',
        ];

        for ($i=0; $i<9; $i++ ) {
            $field = $fields[$i];
            /** @var Nuotrauka $nuotrauka */
            $nuotrauka = $kp->Nuotraukos->{$field};

            if ( $nuotrauka != null && isset($nuotrauka->imageId) && $nuotrauka->imageId != null ) {
                $tmpProductPicture = new TmpProductPicture();

                $tmpProductPicture->priority         = $i+1;
                $tmpProductPicture->sku              = $kp->nomnr;
                $tmpProductPicture->legacy_id        = $nuotrauka->imageId;
                $tmpProductPicture->url              = $nuotrauka->uri;
                $tmpProductPicture->name             = $nuotrauka->fileName;
                $tmpProductPicture->statusas         = PropertiesHelper::truncate($kp->Nuotraukos->statusas, 16 );
                $tmpProductPicture->info_provider    = $kp->Nuotraukos->info_provider;

                $tmpProductPictures[] = $tmpProductPicture;
            }
        }
        return $tmpProductPictures;
    }
}