<?php
namespace Gt\Catalog\Services\Legacy;


use Sketis\B2b\Common\Data\Catalog\KatalogasPreke;
use Sketis\B2b\Common\Data\Catalog\Nuotrauka;

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
        $tmpProduct->info_provider                  = $kp->Atributai->info_provider;
        $tmpProduct->brand                          = $kp->brandas;
        $tmpProduct->line                           = $kp->linija;
        $tmpProduct->vendor                         = $kp->Atributai->tiekejo_kodas;
        $tmpProduct->manufacturer                   = $kp->Atributai->gamintojo_kodas;
        $tmpProduct->type                           = $kp->Atributai->tipas;
        $tmpProduct->purpose                        = $kp->Atributai->paskirtis;
        $tmpProduct->measure                        = $kp->Atributai->matas;
        $tmpProduct->productgroup                   = $kp->Atributai->prekiu_grupe;
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
        $tmpProductLanguage->label_size    = $kp->Aprasymas->etiketes_dydis;
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
                $tmpProductCategory->category = $c->identifikatorius;
                $tmpProductCategory->parent = $c->parent;
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

        $brandC = new TmpClassificator();
        $brandC->group_code = 'brand';
        $brandC->language_code = $langCode;
        $brandC->classificator_code = $kp->brandas;
        $brandC->value = $kp->brandas;
        $tmpClassificators[] = $brandC;

        $lineC = new TmpClassificator();
        $lineC->group_code = 'line';
        $lineC->language_code = $langCode;
        $lineC->classificator_code = $kp->linija;
        $lineC->value = $kp->linija;
        $tmpClassificators[] = $brandC;

        $vendorC = new TmpClassificator();
        $vendorC->group_code = 'vendor';
        $vendorC->classificator_code = $kp->Atributai->tiekejo_kodas;
        $vendorC->value = $kp->Atributai->tiekejo_kodas;
        $vendorC->language_code = $langCode;
        $tmpClassificators[] = $vendorC;

        $manuvacturerC = new TmpClassificator();
        $manuvacturerC->language_code = $langCode;
        $manuvacturerC->group_code = 'manufacturer';
        $manuvacturerC->classificator_code = $kp->Atributai->gamintojo_kodas;
        $manuvacturerC->value = $kp->Atributai->gamintojo_kodas;
        $tmpClassificators[] = $manuvacturerC;

        $typeC = new TmpClassificator();
        $typeC->language_code = $langCode;
        $typeC->group_code = 'type';
        $typeC->classificator_code = $kp->Atributai->tipas;
        $typeC->value = $kp->Atributai->tipas_title;
        $tmpClassificators[] = $typeC;

        $purposeC = new TmpClassificator();
        $purposeC->language_code = $langCode;
        $purposeC->group_code = 'purpose';
        $purposeC->classificator_code = $kp->Atributai->paskirtis;
        $purposeC->value = $kp->Atributai->paskirtis_title;
        $tmpClassificators[] = $purposeC;

        $measureC = new TmpClassificator();
        $measureC->language_code = $langCode;
        $measureC->group_code = 'measure';
        $measureC->classificator_code = $kp->Atributai->matas;
        $measureC->value = $kp->Atributai->matas_title??$kp->Atributai->matas;
        $tmpClassificators[] = $measureC;


        $productGroupC = new TmpClassificator();
        $productGroupC->language_code = $langCode;
        $productGroupC->group_code = 'productgroup';
        $productGroupC->classificator_code = $kp->Atributai->prekiu_grupe;
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

                $tmpProductPictures[] = $tmpProductPicture;
            }
        }
        return $tmpProductPictures;
    }
}