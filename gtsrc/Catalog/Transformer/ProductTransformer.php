<?php

namespace Gt\Catalog\Transformer;

use Catalog\B2b\Common\Data\Catalog\Package;
use Catalog\B2b\Common\Data\Catalog\Product as CatalogProduct;
use Gt\Catalog\Entity\PackageType;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Entity\ProductPackage;
use Gt\Catalog\Utils\ProductsHelper;

class ProductTransformer
{
    public function transformToRestProduct(ProductLanguage $productLanguage): CatalogProduct
    {
        $restProduct = new CatalogProduct();
        $this->mapProductToRestProduct($productLanguage->getProduct(), $restProduct);
        $this->mapProductLanguageToRestProduct($productLanguage, $restProduct);

        return $restProduct;
    }

    private function mapProductToRestProduct(Product $product, CatalogProduct $restProduct): void
    {
        $directMappings = [
            'sku',
            'version',
            'brand',
            'line',
            'parentSku',
            'originCountryCode',
            'vendor',
            'manufacturer',
            'color',
            'forMale',
            'forFemale',
            'size',
            'packSize',
            'packAmount',
            'weight',
            'weightBruto',
            'length',
            'height',
            'width',
            'deliveryTime',
            'depositCode',
            'codeFromCustom',
            'guaranty',
            'codeFromSupplier',
            'codeFromVendor',
            'priority',
            'googleProductCategoryId',
            'lastUpdate',
            'barcode',
        ];

        foreach ($directMappings as $property) {
            $getter = 'get' . ucfirst($property);
            if (method_exists($product, $getter)) {
                $restProduct->$property = $product->$getter();
            }
        }

//        $restProduct->lastUpdate = ProductsHelper::getFormattedDate(
//            $product->getLastUpdate(),
//            ProductsHelper::DATE_FORMAT
//        );
        $restProduct->type = ProductsHelper::getClassificatorCode($product->getType());
        $restProduct->purpose = ProductsHelper::getClassificatorCode($product->getPurpose());
        $restProduct->measure = ProductsHelper::getClassificatorCode($product->getMeasure());
        $restProduct->productgroup = ProductsHelper::getClassificatorCode($product->getProductgroup());

        $restPackages = array_map(
            fn(ProductPackage $pp) => (new Package())
                ->setTypeCode($pp->getPackageType()->getCode())
                ->setWeight($pp->getWeight())
            ,
            $product->getPackages()
        );

        $restProduct->setPackages($restPackages);
    }

    private function mapProductLanguageToRestProduct(
        ProductLanguage $productLanguage,
        CatalogProduct $restProduct
    ): void {
        $restProduct->language = $productLanguage->getLanguageCode();
        $restProduct->name = $productLanguage->getName();
        $restProduct->description = $productLanguage->getDescription();
        $restProduct->label = $productLanguage->getLabel();
        $restProduct->variantName = $productLanguage->getVariantName();
        $restProduct->tags = $productLanguage->getTagsArray();
        $restProduct->labelSize = $productLanguage->getLabelSize();
        $restProduct->distributor = $productLanguage->getDistributor();
        $restProduct->composition = $productLanguage->getComposition();
        $restProduct->ean = $productLanguage->getProduct()->getSku();
    }

    /**
     * Updates db product from dto product. The updated fields written in the array and returned for logging purposes.
     * Fields for updating:
     * - weight
     * - weight_bruto
     * - code_from_custom
     * - packages
     *
     * @param PackageType[] $packagesTypesByCode indexed by a type code.
     *
     * @return string[] updated fields names
     *
     */
    public static function updateSpecialProduct(
        \Catalog\B2b\Common\Data\Catalog\Product $dtoProduct,
        Product $dbProduct,
        array $packagesTypesByCode,
        // will be used later
        int $priority
    ): array {
        $updatedFields = [];

        if ($dtoProduct->weight > 0 && ($dbProduct->getWeight() == 0 || $dbProduct->getUpdatePriority() >= $priority)) {
            $dbProduct->setWeight($dtoProduct->weight);

            $updatedFields[] = 'weight';
        }

        if ($dtoProduct->weightBruto > 0 && ($dbProduct->getWeightBruto() == 0
                || $dbProduct->getUpdatePriority() >= $priority)) {
            $dbProduct->setWeightBruto($dtoProduct->weightBruto);

            $updatedFields[] = 'weight_bruto';
        }

        if ($dtoProduct->codeFromCustom && (empty($dbProduct->getCodeFromCustom())
                || $dbProduct->getUpdatePriority() >= $priority)) {
            $dbProduct->setCodeFromCustom($dtoProduct->codeFromCustom);

            $updatedFields[] = 'code_from_custom';
        }

        if ($dtoProduct->getPackages() && count($dtoProduct->getPackages()) > 0) {
            $dbPackages = $dbProduct->getPackagesDataMap();
            $dtoPackages = self::transformDtoPackagesToMap($dtoProduct->getPackages());

            if (count($dbProduct->getPackages()) == 0
                || $priority <= $dbProduct->getUpdatePriority() && $dbPackages != $dtoPackages
            ) {
                $productsPackages = [];
                foreach ($dtoProduct->getPackages() as $package) {
                    if (!array_key_exists($package->typeCode, $packagesTypesByCode)) {
                        continue;
                    }
                    $productPackage = new ProductPackage();

                    $productPackage->setWeight($package->weight);
                    $productPackage->setPackageType($packagesTypesByCode[$package->typeCode]);

                    // using typeCode in a key to avoid duplicate records with the same package type
                    $productsPackages[$package->typeCode] = $productPackage;
                }

                if (count($productsPackages) != 0) {
                    $dbProduct->setProductsPackages(array_values($productsPackages));

                    $updatedFields[] = 'packages';
                }
            }
        }

        if ($dbProduct->getBarcode() == null
            || ($priority <= $dbProduct->getUpdatePriority() && $dbProduct->getBarcode() != $dtoProduct->barcode)) {

            $dbProduct->setBarcode($dtoProduct->barcode);
        }

        if (count($updatedFields) > 0 && $dbProduct->getUpdatePriority() >= $priority) {
            $dbProduct->setUpdatePriority($priority);
        }

        return $updatedFields;
    }


    /**
     * TODO move to DTO product class later
     * @param Package[] $dtoPackages
     * @return float[] keys are packages types
     */
    public static function transformDtoPackagesToMap(array $dtoPackages): array
    {
        $rez = [];

        foreach ($dtoPackages as $package) {
            $rez[$package->typeCode] = $package->weight;
        }

        return $rez;
    }
}
