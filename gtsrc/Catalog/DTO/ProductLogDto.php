<?php

namespace Gt\Catalog\DTO;

class ProductLogDto
{
    /**
     * @var mixed
     */
    private $sku;

    /**
     * @var mixed
     */
    private $version;

    /**
     * @var mixed
     */
    private $brand;

    /**
     * @var mixed
     */
    private $line;
    /**
     * @var mixed
     */
    private $parentSku;
    /**
     * @var mixed
     */
    private $barcode;
    /**
     * @var mixed
     */
    private $originCountryCode;
    /**
     * @var mixed
     */
    private $vendor;
    /**
     * @var mixed
     */
    private $codeFromCustom;
    /**
     * @var mixed
     */
    private $manufacturer;
    /**
     * @var mixed
     */
    private $purposeCode;
    /**
     * @var mixed
     */
    private $typeCode;
    /**
     * @var mixed
     */
    private $measureCode;
    /**
     * @var mixed
     */
    private $color;
    /**
     * @var mixed
     */
    private $forFemale;
    /**
     * @var mixed
     */
    private $forMale;
    /**
     * @var mixed
     */
    private $size;
    /**
     * @var mixed
     */
    private $packSize;
    /**
     * @var mixed
     */
    private $packAmount;
    /**
     * @var mixed
     */
    private $weight;
    /**
     * @var mixed
     */
    private $lenght;
    /**
     * @var mixed
     */
    private $height;
    /**
     * @var mixed
     */
    private $width;
    /**
     * @var mixed
     */
    private $deliveryTime;
    /**
     * @var mixed
     */
    private $priority;
    /**
     * @var mixed
     */
    private $googleProductCategoryId;
    /**
     * @var mixed
     */
    private $infoProvider;
    /**
     * @var mixed
     */
    private $ingredients;

    /**
     * @var mixed
     */
    private $WeightBruto;

    public function getSku()
    {
        return $this->sku;
    }

    public function setSku($sku): void
    {
        $this->sku = $sku;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version): void
    {
        $this->version = $version;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function setBrand($brand): void
    {
        $this->brand = $brand;
    }


    public function getLine()
    {
        return $this->line;
    }

    public function setLine($line): void
    {
        $this->line = $line;
    }

    public function getParentSku()
    {
        return $this->parentSku;
    }

    public function setParentSku($parentSku): void
    {
        $this->parentSku = $parentSku;
    }

    public function getBarcode()
    {
        return $this->barcode;
    }

    public function setBarcode($barcode): void
    {
        $this->barcode = $barcode;
    }

    public function getOriginCountryCode()
    {
        return $this->originCountryCode;
    }

    public function setOriginCountryCode($originCountryCode): void
    {
        $this->originCountryCode = $originCountryCode;
    }

    public function getVendor()
    {
        return $this->vendor;
    }

    public function setVendor($vendor): void
    {
        $this->vendor = $vendor;
    }

    public function getManufacturer() {
        return $this->manufacturer;
    }

    public function setManufacturer($manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    public function getPurposeCode()
    {
        return $this->purposeCode;
    }

    public function setPurposeCode($purposeCode): void
    {
        $this->purposeCode = $purposeCode;
    }

    public function getTypeCode()
    {
        return $this->typeCode;
    }

    public function setTypeCode($typeCode): void
    {
        $this->typeCode = $typeCode;
    }

    public function getMeasureCode()
    {
        return $this->measureCode;
    }

    public function setMeasureCode($measureCode): void
    {
        $this->measureCode = $measureCode;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function setColor($color): void
    {
        $this->color = $color;
    }

    public function getForMale()
    {
        return $this->forMale;
    }

    public function setForMale($forMale): void
    {
        $this->forMale = $forMale;
    }

    public function getForFemale()
    {
        return $this->forFemale;
    }

    public function setForFemale($forFemale): void
    {
        $this->forFemale = $forFemale;
    }
    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size): void
    {
        $this->size = $size;
    }

    public function getPackSize()
    {
        return $this->packSize;
    }

    public function setPackSize($packSize): void
    {
        $this->packSize = $packSize;
    }

    public function getPackAmount()
    {
        return $this->packAmount;
    }

    public function setPackAmount($packAmount): void
    {
        $this->packAmount = $packAmount;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setWeight($weight): void
    {
        $this->weight = $weight;
    }

    public function getLength()
    {
        return $this->lenght;
    }

    public function setLength($lenght): void
    {
        $this->lenght = $lenght;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height): void
    {
        $this->height = $height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width): void
    {
        $this->width = $width;
    }

    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime($deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority): void
    {
        $this->priority = $priority;
    }

    public function getGoogleProductCategoryId()
    {
        return $this->googleProductCategoryId;
    }

    public function setGoogleProductCategoryId($googleProductCategoryId): void
    {
        $this->googleProductCategoryId = $googleProductCategoryId;
    }

    public function getInfoProvider()
    {
        return $this->infoProvider;
    }

    public function setInfoProvider($infoProvider): void
    {
        $this->infoProvider = $infoProvider;
    }
    public function getIngredients()
    {
        return $this->ingredients;
    }

    public function setIngredients($ingredients): void
    {
        $this->ingredients = $ingredients;
    }

    public function getCodeFromCustom()
    {
        return $this->codeFromCustom;
    }

    public function setCodeFromCustom($codeFromCustom): void
    {
        $this->codeFromCustom = $codeFromCustom;
    }

    public function getWeightBruto()
    {
        return  $this->WeightBruto;
    }

    public function setWeightBruto($WeightBruto)
    {
        $this->WeightBruto = $WeightBruto;
    }
}