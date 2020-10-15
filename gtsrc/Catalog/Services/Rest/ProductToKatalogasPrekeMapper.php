<?php
/**
 * ProductToPrekeMapper.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-15
 * Time: 15:27
 */

namespace Gt\Catalog\Services\Rest;


use Gt\Catalog\Entity\CategoryLanguage;
use Gt\Catalog\Entity\Picture;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Rest\Legacy\Aprasymas;
use Gt\Catalog\Rest\Legacy\Atributai;
use Gt\Catalog\Rest\Legacy\KatalogasPreke;
use Gt\Catalog\Rest\Legacy\Klasifikatorius;
use Gt\Catalog\Rest\Legacy\Nuotrauka;
use Gt\Catalog\Rest\Legacy\Nuotraukos;
use Gt\Catalog\Utils\PropertiesHelper;

class ProductToKatalogasPrekeMapper
{
    /**
     * @param ProductLanguage $pl
     * @param CategoryLanguage[] $categoriesLanguages
     * @param Picture[] $pictures
     * @return KatalogasPreke
     */
    public static function mapProduct2KatalogasPreke(ProductLanguage $pl, $categoriesLanguages, $pictures) {
        $kp = new KatalogasPreke();


        $kp->nomnr            = $pl->getProduct()->getSku();
        $kp->pavadinimas      = $pl->getName() ;
        $kp->brandas          = PropertiesHelper::getPropertyOrNull( $pl->getProduct()->getBrand(),  'code' );
        $kp->linija           = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getLine(), 'code');
        $kp->depozito_kodas   = ''; // TODO field
        $kp->muitines_kodas   = ''; // TODO field
        $kp->origin_country   = $pl->getProduct()->getOriginCountryCode();
        $kp->parent           = $pl->getProduct()->getParentSku();
        $kp->info_provider    = $pl->getProduct()->getInfoProvider();
        $kp->tags             = $pl->getTags() ;


        $kp->Atributai->nomnr                                = $pl->getProduct()->getSku();
        $kp->Atributai->spalva                               = $pl->getProduct()->getColor();
        $kp->Atributai->garantija                            = ''; // TODO field
        $kp->Atributai->tiekejo_kodas                        = ''; // TODO field
        $kp->Atributai->gamintojo_kodas                      = ''; // TODO field
        $kp->Atributai->svoris                               = $pl->getProduct()->getWeight();
        $kp->Atributai->ilgis                                = $pl->getProduct()->getLength();
        $kp->Atributai->aukstis                              = $pl->getProduct()->getHeight();
        $kp->Atributai->plotis                               = $pl->getProduct()->getWidth();
        $kp->Atributai->pristatymo_laikas                    = $pl->getProduct()->getDeliveryTime();
        $kp->Atributai->tipas                                = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getType(), 'code');
//        $kp->Atributai->tipas_title                          = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getType(), 'name');
        $kp->Atributai->paskirtis                            = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getPurpose(), 'code');
//        $kp->Atributai->paskirtis_title                      = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getPurpose(), 'name');
        $kp->Atributai->vyrams                               = $pl->getProduct()->getForMale();
        $kp->Atributai->moterims                             = $pl->getProduct()->getForFemale();
        $kp->Atributai->dydis                                = $pl->getProduct()->getSize();
        $kp->Atributai->kiekis                               = $pl->getProduct()->getPackAmount();
        $kp->Atributai->matas                                = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getMeasure(), 'code');
//        $kp->Atributai->matas_title                          = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getMeasure(), 'name');       ;
        $kp->Atributai->tagai                                = $pl->getTags();
        $kp->Atributai->pack_size                            = $pl->getProduct()->getPackSize();
//        $kp->Atributai->prekiu_grupe                         =        ; // TODO group field
//        $kp->Atributai->prekiu_grupe_title                   =        ; // TODO
//        $kp->Atributai->priority                             =        ; // TODO
//        $kp->Atributai->google_product_category              =        ; // TODO field and additional object
//        $kp->Atributai->google_product_category_title        =        ; // TODO

        $categories = [];
        $categoriesTitles = [];
        // TODO

//        $kp->Atributai->kategorijos                          =        ; // TODO
//        $kp->Atributai->kategorijos_titles                   =        ; // TODO


//        $kp->categories       = ; // TODO

        $kp->Aprasymas->pavadinimas            = $pl->getName();
        $kp->Aprasymas->aprasymas              = $pl->getDescription();
        $kp->Aprasymas->ilgas_aprasymas        = $pl->getDescription();
        $kp->Aprasymas->etiketes_tekstas       = $pl->getLabel();
//        $kp->Aprasymas->etiketes_dydis         = ; // TODO field
        $kp->Aprasymas->gamintojas             = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getVendor(), 'code');
//        $kp->Aprasymas->platintojas            = ; // TODO field
//        $kp->Aprasymas->sudetis                = ; // TODO field
        $kp->Aprasymas->info_provider          = $pl->getInfoProvider();
        $kp->Aprasymas->tagai                  = $pl->getTags();
//        $kp->Aprasymas->nuotrauka              = ; // TODO gal čia pirmąją nuotrauką sukišti?
//        $kp->Aprasymas->remote_id              = ;
        $kp->Aprasymas->modificationTimestamp  = $pl->getProduct()->getLastUpdate();
        $kp->Aprasymas->var_name               = $pl->getVariantName();



//        $kp->Nuotraukos       = ; // TODO

        return $kp;
    }
}