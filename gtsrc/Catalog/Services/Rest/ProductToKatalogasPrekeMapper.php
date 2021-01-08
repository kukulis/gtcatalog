<?php
/**
 * ProductToPrekeMapper.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-15
 * Time: 15:27
 */

namespace Gt\Catalog\Services\Rest;


use Catalog\B2b\Common\Data\Legacy\Catalog\KatalogasPreke;
use Catalog\B2b\Common\Data\Legacy\Catalog\Klasifikatorius;
use Catalog\B2b\Common\Data\Legacy\Catalog\Nuotrauka;
use Gt\Catalog\Entity\CategoryLanguage;
use Gt\Catalog\Entity\ClassificatorLanguage;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Entity\ProductPicture;
use Gt\Catalog\Utils\PicturesHelper;
use Gt\Catalog\Utils\PropertiesHelper;

class ProductToKatalogasPrekeMapper
{
    /**
     * @param ProductLanguage $pl
     * @param CategoryLanguage[] $categoriesLangs
     * @param ProductPicture[] $productPictures
     * @param ClassificatorLanguage [] $clMap   key:group_code
     * @return KatalogasPreke
     */
    public static function mapProduct2KatalogasPreke(ProductLanguage $pl, $categoriesLangs, $productPictures, $clMap) {
        $kp = new KatalogasPreke();

        $kp->nomnr            = $pl->getProduct()->getSku();
        $kp->pavadinimas      = $pl->getName() ;
        $kp->brandas          = PropertiesHelper::getPropertyOrNull( $pl->getProduct()->getBrand(),  'code' );
        $kp->linija           = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getLine(), 'code');
        $kp->depozito_kodas   = $pl->getProduct()->getDepositCode();
        $kp->muitines_kodas   = $pl->getProduct()->getCodeFromCustom();
        $kp->origin_country   = $pl->getProduct()->getOriginCountryCode();
        $kp->parent           = $pl->getProduct()->getParentSku();
        $kp->info_provider    = $pl->getProduct()->getInfoProvider();
        $kp->tags             = $pl->getTags() ;

        $kp->Atributai->nomnr                                = $pl->getProduct()->getSku();
        $kp->Atributai->spalva                               = $pl->getProduct()->getColor();
        $kp->Atributai->garantija                            = $pl->getProduct()->getGuaranty();
        $kp->Atributai->tiekejo_kodas                        = $pl->getProduct()->getCodeFromSupplier();
        $kp->Atributai->gamintojo_kodas                      = $pl->getProduct()->getCodeFromVendor();
        $kp->Atributai->svoris                               = $pl->getProduct()->getWeight();
        $kp->Atributai->ilgis                                = $pl->getProduct()->getLength();
        $kp->Atributai->aukstis                              = $pl->getProduct()->getHeight();
        $kp->Atributai->plotis                               = $pl->getProduct()->getWidth();
        $kp->Atributai->pristatymo_laikas                    = $pl->getProduct()->getDeliveryTime();
        $kp->Atributai->tipas                                = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getType(), 'code');
        $kp->Atributai->tipas_title                          = PropertiesHelper::getPropertyFromMap($clMap, 'type', 'name' );
        $kp->Atributai->paskirtis                            = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getPurpose(), 'code');
        $kp->Atributai->paskirtis_title                      = PropertiesHelper::getPropertyFromMap($clMap, 'purpose', 'name' );
        $kp->Atributai->vyrams                               = $pl->getProduct()->getForMale();
        $kp->Atributai->moterims                             = $pl->getProduct()->getForFemale();
        $kp->Atributai->dydis                                = $pl->getProduct()->getSize();
        $kp->Atributai->kiekis                               = $pl->getProduct()->getPackAmount();
        $kp->Atributai->matas                                = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getMeasure(), 'code');
        $kp->Atributai->matas_title                          = PropertiesHelper::getPropertyFromMap($clMap, 'measure', 'name' );
        $kp->Atributai->tagai                                = $pl->getTags();
        $kp->Atributai->pack_size                            = $pl->getProduct()->getPackSize();
        $kp->Atributai->prekiu_grupe                         = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getProductGroup(), 'code');
        $kp->Atributai->prekiu_grupe_title                   = PropertiesHelper::getPropertyFromMap($clMap, 'productgroup', 'name' );
        $kp->Atributai->priority                             = $pl->getProduct()->getPriority();
        $kp->Atributai->google_product_category              = $pl->getProduct()->getGoogleProductCategoryId();
//        $kp->Atributai->google_product_category_title        =        ; // TODO kažkada vėliau šitą

        $categoriesCodes = [];
        $categoriesTitles = [];

        /** @var Klasifikatorius[] $categoriesClassificators */
        $categoriesClassificators = [];

        foreach ($categoriesLangs as $c ) {
            $categoryClassificator = new Klasifikatorius();
            $categoriesCodes[] = $c->getCode();
            $categoriesTitles[] = $c->getName();

            $categoryClassificator->parent = $c->getCategory()->getParentCode();
            $categoryClassificator->title = $c->getName();
            $categoryClassificator->identifikatorius = $c->getCode();
            $categoryClassificator->atributas = 'kategorijos';
            $categoriesClassificators[]  = $categoryClassificator;
        }

        $kp->Atributai->kategorijos = $categoriesCodes; // join ( ',', $categoriesCodes);
        $kp->Atributai->kategorijos_titles = $categoriesTitles; // join (',', $categoriesTitles);
        $kp->categories = $categoriesClassificators;

        $kp->Aprasymas->pavadinimas            = $pl->getName();
        $kp->Aprasymas->aprasymas              = $pl->getDescription();
        $kp->Aprasymas->ilgas_aprasymas        = $pl->getDescription();
        $kp->Aprasymas->etiketes_tekstas       = $pl->getLabel();
        $kp->Aprasymas->etiketes_dydis         = $pl->getLabelSize();
        $kp->Aprasymas->gamintojas             = PropertiesHelper::getPropertyOrNull($pl->getProduct()->getVendor(), 'code');
        $kp->Aprasymas->platintojas            = $pl->getDistributor();
        $kp->Aprasymas->sudetis                = $pl->getComposition();
        $kp->Aprasymas->info_provider          = $pl->getInfoProvider();
        $kp->Aprasymas->tagai                  = $pl->getTags();
//        $kp->Aprasymas->nuotrauka              = ; // šitus paliekam
//        $kp->Aprasymas->remote_id              = ; // šitus paliekam
        $kp->Aprasymas->modificationTimestamp  = $pl->getProduct()->getLastUpdate();
        $kp->Aprasymas->var_name               = $pl->getVariantName();

        if ( count($productPictures ) > 0 ) {
            $productPictures = array_slice($productPictures, 0, 9); // taking only first 9 pictures

            // sort by priority
            usort($productPictures, [ProductPicture::class, 'lambdaComparePriority']);

            // first picture id is the "version" of the pictures array
            // we assume that the configuredPath is calculated for each picture
            $kp->Nuotraukos->versija = $productPictures[0]->getPicture()->getId();

            $nuotraukosProperties = [
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

            // pictures are sorted by priority 18 lines above with "usort"
            for ( $i=0; $i < count($productPictures); $i++) {
                $pp = $productPictures[$i];
                $property = $nuotraukosProperties[$i];

                $nuotrauka = new Nuotrauka();
                $nuotrauka->uri = $pp->getPicture()->getConfiguredPath();
                $nuotrauka->fileName = $pp->getPicture()->getName();
                $nuotrauka->imageId = $pp->getPicture()->getId();
                $nuotrauka->id = $pp->getPicture()->getId();

                if ($nuotrauka->uri != null) {
                    $nuotrauka->uri = PicturesHelper::prefixWithSlash($nuotrauka->uri);
                }

                $kp->Nuotraukos->{$property} = $nuotrauka;
            }
        }

        $kp->kalba = $pl->getLanguage()->getCode();
        return $kp;
    }
}