<?php

namespace Gt\Catalog\Transformer;

use Catalog\B2b\Common\Data\Catalog\Product as CatalogProduct;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Utils\ProductsHelper;

class ProductTransformer {
    public function transformToRestProduct(ProductLanguage $productLanguage): CatalogProduct {
        $restProduct = new CatalogProduct();
        $this->mapProductToRestProduct($productLanguage->getProduct(), $restProduct);
        $this->mapProductLanguageToRestProduct($productLanguage, $restProduct);

        return $restProduct;
    }

    // TODO check if works without &
    private function mapProductToRestProduct(Product $product, CatalogProduct $restProduct): void {
        $directMappings = [
            'sku', 'version', 'brand', 'line', 'parentSku', 'originCountryCode',
            'vendor', 'manufacturer', 'color', 'forMale', 'forFemale', 'size',
            'packSize', 'packAmount', 'weight', 'length', 'height', 'width',
            'deliveryTime', 'depositCode', 'codeFromCustom', 'guaranty', 'codeFromSupplier',
            'codeFromVendor', 'priority', 'googleProductCategoryId'
        ];

        foreach ($directMappings as $property) {
            $getter = 'get' . ucfirst($property);
            if (method_exists($product, $getter)) {
                $restProduct->$property = $product->$getter();
            }
        }

        $restProduct->lastUpdate = ProductsHelper::getFormattedDate($product->getLastUpdate(), ProductsHelper::DATE_FORMAT);
        $restProduct->type = ProductsHelper::getClassificatorCode($product->getType());
        $restProduct->purpose = ProductsHelper::getClassificatorCode($product->getPurpose());
        $restProduct->measure = ProductsHelper::getClassificatorCode($product->getMeasure());
        $restProduct->productgroup = ProductsHelper::getClassificatorCode($product->getProductgroup());
    }

    private function mapProductLanguageToRestProduct(ProductLanguage $productLanguage, CatalogProduct $restProduct): void {
        $restProduct->language = $productLanguage->getLanguage()->getCode();
        $restProduct->name = $productLanguage->getName();
        $restProduct->description = $productLanguage->getDescription();
        $restProduct->label = $productLanguage->getLabel();
        $restProduct->variantName = $productLanguage->getVariantName();
        $restProduct->tags = $productLanguage->getTags();
        $restProduct->labelSize = $productLanguage->getLabelSize();
        $restProduct->distributor = $productLanguage->getDistributor();
        $restProduct->composition = $productLanguage->getComposition();
        $restProduct->ean = $productLanguage->getProduct()->getSku();
    }
}
