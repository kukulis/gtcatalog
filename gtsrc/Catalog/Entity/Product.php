<?php

namespace Gt\Catalog\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gt\Catalog\Exception\CatalogErrorException;

/**
 * @ORM\Entity(repositoryClass=Gt\Catalog\Repository\ProductsRepository::class)
 * @ORM\Table(name="products")
 */
class Product
{
    public function __construct()
    {
        $this->productCategories = new ArrayCollection();
    }

    const CLASSIFICATORS_GROUPS = [
//        'brand',
//        'line',
//        'manufacturer',
        'measure',
        'purpose',
        'type',
//        'vendor',
        'productgroup',
    ];

    const ALLOWED_FIELDS = [
        'last_update',
        'version',
        'brand',
        'line',
        'parent_sku',
        'origin_country_code',
        'vendor',
        'manufacturer',
        'purpose',
        'type',
        'measure',
        'color',
        'for_male',
        'for_female',
        'size',
        'pack_size',
        'pack_amount',
        'weight',
        'weight_bruto',
        'length',
        'height',
        'width',
        'delivery_time',
        'priority',
        'google_product_category_id',
        'info_provider',
        'ingredients',
        'code_from_custom'
    ];


    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=32)
     */
    private $sku;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     *
     */
    private $lastUpdate;

    /**
     * @var int
     * @ORM\Column(type="integer", options={ "default":0})
     */
    private $version = 0;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, name="brand", nullable=true)
     */
    private $brand;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, name="line", nullable=true)
     */
    private $line;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, name="parent_sku", nullable=true)
     */
    private $parentSku;

    /**
     * @var string
     * @ORM\Column(type="string", length=3, name="origin_country_code", nullable=true)
     */
    private $originCountryCode;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, name="vendor", nullable=true)
     */
    private $vendor;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, name="manufacturer", nullable=true)
     */
    private $manufacturer;

    /**
     * @var Classificator
     * @ORM\ManyToOne(targetEntity="Classificator" )
     * @ORM\JoinColumn(name="type", referencedColumnName="code")
     */
    private $type;

    /**
     * @var Classificator
     * @ORM\ManyToOne(targetEntity="Classificator" )
     * @ORM\JoinColumn(name="purpose", referencedColumnName="code")
     */
    private $purpose;

    /**
     * @var Classificator
     * @ORM\ManyToOne(targetEntity="Classificator" )
     * @ORM\JoinColumn(name="measure", referencedColumnName="code")
     */
    private $measure;


    /**
     * @var string
     * @ORM\Column(type="string", name="color", nullable=true)
     */
    private $color;

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="for_male", options={"default":0})
     */
    private $forMale = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="for_female", options={"default":0})
     */
    private $forFemale = false;

    /**
     * @var string
     * @ORM\Column(type="string", name="size", nullable=true)
     */
    private $size;

    /**
     * @var string
     * @ORM\Column(type="string", name="pack_size", nullable=true)
     */
    private $packSize;

    /**
     * @var string
     * @ORM\Column(type="string", name="pack_amount", nullable=true)
     */
    private $packAmount;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, precision=10, options={"default":0} )
     */
    private $weight = 0.0;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, precision=10, options={"default": 0})
     */
    private $weightBruto = 0.0;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, precision=10, options={"default":0} )
     */
    private $length = 0.0;
    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, precision=10, options={"default":0} )
     */
    private $height = 0.0;
    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, precision=10, options={"default":0} )
     */
    private $width = 0.0;

    /**
     * @var string
     * @ORM\Column(type="string", name="delivery_time", nullable=true)
     */
    private $deliveryTime;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, name="info_provider", nullable=true)
     */
    private $infoProvider;


    /**
     * @var string
     * @ORM\Column(type="string", length=32, name="deposit_code", nullable=true)
     */
    private $depositCode;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, name="code_from_custom", nullable=true)
     */
    private $codeFromCustom;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, name="guaranty", nullable=true)
     */
    private $guaranty;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, name="code_from_supplier", nullable=true)
     */
    private $codeFromSupplier;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, name="code_from_vendor", nullable=true)
     */
    private $codeFromVendor;

    /**
     * @var Classificator
     * @ORM\ManyToOne(targetEntity="Classificator" )
     * @ORM\JoinColumn(name="productgroup", referencedColumnName="code")
     */
    private $productgroup;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, name="priority", nullable=true)
     */
    private $priority;

    /**
     * @var int
     * @ORM\Column(type="integer", name="google_product_category_id", nullable=true)
     */
    private $googleProductCategoryId = 0;

    /**
     * @var ?string
     * @ORM\Column(type="text", nullable=true)
     */
    private $ingredients = "";

    /**
     * @var string
     * @ORM\Column(type="string", length=64, name="label_material", nullable=true)
     */
    private $labelMaterial;


    /** @var string */
    private $extractedName; // not stored in database

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="ProductCategory", mappedBy="product" )
     */
    private $productCategories;

    /**
     * May be configured for doctrine later.
     *
     * @var ProductPackage[]
     */
    private $packages = [];

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default":2} )
     */
    private $updatePriority = 2;

    /**
     * @return string
     */
    public function getSku(): ?string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdate(): ?DateTime
    {
        return $this->lastUpdate;
    }

    /**
     * @param \DateTime $lastUpdate
     */
    public function setLastUpdate(DateTime $lastUpdate = null): void
    {
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion(int $version): void
    {
        $this->version = $version;
    }


    /**
     * @return string
     */
    public function getParentSku(): ?string
    {
        return $this->parentSku;
    }

    /**
     * @param string $parentSku
     */
    public function setParentSku(string $parentSku = null): void
    {
        $this->parentSku = $parentSku;
    }

    /**
     * @return string
     */
    public function getInfoProvider(): ?string
    {
        return $this->infoProvider;
    }

    /**
     * @param string $infoProvider
     */
    public function setInfoProvider(string $infoProvider = null): void
    {
        $this->infoProvider = $infoProvider;
    }

    /**
     * @return string
     */
    public function getOriginCountryCode(): ?string
    {
        return $this->originCountryCode;
    }

    /**
     * @param string $originCountryCode
     */
    public function setOriginCountryCode(string $originCountryCode = null): void
    {
        $this->originCountryCode = $originCountryCode;
    }

    /**
     * @return Classificator
     */
    public function getType(): ?Classificator
    {
        return $this->type;
    }

    public function getTypeCode(): ?string
    {
        if ($this->type == null) {
            return null;
        }

        return $this->type->getCode();
    }

    /**
     * @param Classificator $type
     */
    public function setType(Classificator $type = null): void
    {
        $this->type = $type;
    }

    /**
     * @return Classificator
     */
    public function getPurpose(): ?Classificator
    {
        return $this->purpose;
    }

    public function getPurposeCode(): ?string
    {
        if (is_null($this->purpose)) {
            return null;
        }

        return $this->purpose->getCode();
    }

    /**
     * @param Classificator $purpose
     */
    public function setPurpose(Classificator $purpose = null): void
    {
        $this->purpose = $purpose;
    }

    /**
     * @return Classificator
     */
    public function getMeasure(): ?Classificator
    {
        return $this->measure;
    }

    public function getMeasureCode(): ?string
    {
        if ($this->measure == null) {
            return null;
        }

        return $this->measure->getCode();
    }

    /**
     * @param Classificator $measure
     */
    public function setMeasure(Classificator $measure = null): void
    {
        $this->measure = $measure;
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color = null): void
    {
        $this->color = $color;
    }

    /**
     * @return bool
     */
    public function isForMale(): bool
    {
        return $this->forMale;
    }

    /**
     * @return bool
     */
    public function getForMale(): bool
    {
        return $this->forMale;
    }

    /**
     * @param bool $forMale
     */
    public function setForMale(bool $forMale): void
    {
        $this->forMale = $forMale;
    }

    /**
     * @return bool
     */
    public function isForFemale(): bool
    {
        return $this->forFemale;
    }

    /**
     * @return bool
     */
    public function getForFemale(): bool
    {
        return $this->forFemale;
    }

    /**
     * @param bool $forFemale
     */
    public function setForFemale(bool $forFemale): void
    {
        $this->forFemale = $forFemale;
    }

    /**
     * @return string
     */
    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize(string $size = null): void
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getPackSize(): ?string
    {
        return $this->packSize;
    }

    /**
     * @param string $packSize
     */
    public function setPackSize(string $packSize = null): void
    {
        $this->packSize = $packSize;
    }

    /**
     * @return string
     */
    public function getPackAmount(): ?string
    {
        return $this->packAmount;
    }

    /**
     * @param string $packAmount
     */
    public function setPackAmount(string $packAmount = null): void
    {
        $this->packAmount = $packAmount;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight(float $weight)
    {
        $this->weight = $weight;

        return $this;
    }

    public function getWeightBruto()
    {
        return $this->weightBruto;
    }

    public function setWeightBruto($weightBruto)
    {
        $this->weightBruto = $weightBruto;
        return $this;
    }

    /**
     * @return float
     */
    public function getLength(): float
    {
        return $this->length;
    }

    /**
     * @param float $length
     */
    public function setLength(float $length): self
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * @param float $height
     */
    public function setHeight(float $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * @param float $width
     */
    public function setWidth(float $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryTime(): ?string
    {
        return $this->deliveryTime;
    }

    /**
     * @param string $deliveryTime
     */
    public function setDeliveryTime(string $deliveryTime = null): self
    {
        $this->deliveryTime = $deliveryTime;

        return $this;
    }

    /**
     * @param Classificator $classificator
     * @throws CatalogErrorException
     */
    public function setClassificator(Classificator $classificator)
    {
        $groupCode = $classificator->getClassificatorGroup()->getCode();
        if (array_search($groupCode, self::CLASSIFICATORS_GROUPS) !== false) {
            $setter = 'set' . $groupCode;
            $this->{$setter}($classificator);
        } else {
            throw new CatalogErrorException('Neteisingas klasifikatoriaus grupės kodas ' . $groupCode);
        }
    }

    /**
     * @param Product $p
     * @return string
     * @deprecated not needed with arrow functions.
     */
    public static function lambdaGetSku(Product $p)
    {
        return $p->getSku();
    }

    /**
     * @return string
     */
    public function getExtractedName(): string
    {
        return $this->extractedName;
    }

    /**
     * @param string $extractedName
     */
    public function setExtractedName(string $extractedName): void
    {
        $this->extractedName = $extractedName;
    }

    /**
     * @return string
     */
    public function getDepositCode(): ?string
    {
        return $this->depositCode;
    }

    /**
     * @param string $depositCode
     */
    public function setDepositCode(string $depositCode = null): void
    {
        $this->depositCode = $depositCode;
    }

    /**
     * @return string
     */
    public function getCodeFromCustom(): ?string
    {
        return $this->codeFromCustom;
    }

    /**
     * @param string $codeFromCustom
     */
    public function setCodeFromCustom(string $codeFromCustom = null): self
    {
        $this->codeFromCustom = $codeFromCustom;

        return $this;
    }

    /**
     * @return string
     */
    public function getGuaranty(): ?string
    {
        return $this->guaranty;
    }

    /**
     * @param string $guaranty
     */
    public function setGuaranty(string $guaranty = null): void
    {
        $this->guaranty = $guaranty;
    }

    /**
     * @return string
     */
    public function getCodeFromSupplier(): ?string
    {
        return $this->codeFromSupplier;
    }

    /**
     * @param string $codeFromSupplier
     */
    public function setCodeFromSupplier(string $codeFromSupplier = null): void
    {
        $this->codeFromSupplier = $codeFromSupplier;
    }

    /**
     * @return string
     */
    public function getCodeFromVendor(): ?string
    {
        return $this->codeFromVendor;
    }

    /**
     * @param string $codeFromVendor
     */
    public function setCodeFromVendor(string $codeFromVendor = null): void
    {
        $this->codeFromVendor = $codeFromVendor;
    }

    /**
     * @return Classificator
     */
    public function getProductGroup(): ?Classificator
    {
        return $this->productgroup;
    }

    /**
     * @param Classificator $productgroup
     */
    public function setProductGroup(Classificator $productgroup = null): void
    {
        $this->productgroup = $productgroup;
    }

    /**
     * @return string
     */
    public function getPriority(): ?string
    {
        return $this->priority;
    }

    /**
     * @param string $priority
     */
    public function setPriority(string $priority = null): void
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getGoogleProductCategoryId(): ?int
    {
        return $this->googleProductCategoryId;
    }

    /**
     * @param int $googleProductCategoryId
     */
    public function setGoogleProductCategoryId(int $googleProductCategoryId): void
    {
        $this->googleProductCategoryId = $googleProductCategoryId;
    }

    /**
     * @return string
     */
    public function getBrand(): ?string
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     */
    public function setBrand(string $brand = null): void
    {
        $this->brand = $brand;
    }

    /**
     * @return string
     */
    public function getLine(): ?string
    {
        return $this->line;
    }

    /**
     * @param string $line
     */
    public function setLine(string $line = null): void
    {
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    /**
     * @param string $vendor
     */
    public function setVendor(string $vendor = null): void
    {
        $this->vendor = $vendor;
    }

    /**
     * @return string
     */
    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    /**
     * @param string $manufacturer
     */
    public function setManufacturer(string $manufacturer = null): void
    {
        $this->manufacturer = $manufacturer;
    }

    public function getIngredients(): ?string
    {
        return $this->ingredients;
    }

    public function setIngredients(?string $ingredients): void
    {
        $this->ingredients = $ingredients;
    }

    public function getLabelMaterial(): ?string
    {
        return $this->labelMaterial;
    }

    public function setLabelMaterial(string $labelMaterial = null): void
    {
        $this->labelMaterial = $labelMaterial;
    }

    public function getProductCategories(): Collection
    {
        return $this->productCategories;
    }

    public function setProductCategories(Collection $productCategories): void
    {
        $this->productCategories = $productCategories;
    }

    /**
     * @return ProductPackage[]
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    /**
     * @param ProductPackage[] $packages
     *
     * @return $this
     */
    public function setProductsPackages(array $packages): Product
    {
        $this->packages = $packages;
        array_walk($this->packages, fn(ProductPackage $package) => $package->setProduct($this));

        return $this;
    }

    public function addProductPackage(ProductPackage $pp): self
    {
        $pp->setProduct($this);

        $this->packages[] = $pp;

        return $this;
    }

    public function getUpdatePriority(): int
    {
        return $this->updatePriority;
    }

    public function setUpdatePriority(int $updatePriority): Product
    {
        $this->updatePriority = $updatePriority;
        return $this;
    }

    /**
     * @return float[] - key is package type
     */
    public function getPackagesDataMap(): array
    {
        $rez = [];
        foreach ($this->packages as $package) {
            $rez[$package->getPackageType()->getCode()] = $package->getWeight();
        }

        return $rez;
    }

    public function hasNewPackage(): bool
    {
        foreach ($this->packages as $package) {
            if ($package->getId() == 0) {
                return true;
            }
        }
        return false;
    }
}