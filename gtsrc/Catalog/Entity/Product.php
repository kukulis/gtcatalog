<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 11.29
 */

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gt\Catalog\Exception\CatalogErrorException;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="products")
 */
class Product
{
    const CLASSIFICATORS_GROUPS = [
        'brand',
        'line',
        'manufacturer',
        'measure',
        'purpose',
        'type',
        'vendor',
        'productgroup',
        'purpose'
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
        'length',
        'height',
        'width',
        'delivery_time'
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
    private $version=0;

    /**
     * @var Classificator
     * @ORM\ManyToOne(targetEntity="Classificator" )
     * @ORM\JoinColumn(name="brand", referencedColumnName="code")
     */
    private $brand='';

    /**
     * @var Classificator
     * @ORM\ManyToOne(targetEntity="Classificator" )
     * @ORM\JoinColumn(name="line", referencedColumnName="code")
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
     * @var Classificator
     * @ORM\ManyToOne(targetEntity="Classificator" )
     * @ORM\JoinColumn(name="vendor", referencedColumnName="code")
     */
    private $vendor;

    /**
     * @var Classificator
     * @ORM\ManyToOne(targetEntity="Classificator" )
     * @ORM\JoinColumn(name="manufacturer", referencedColumnName="code")
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
    private $forMale=false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="for_female", options={"default":0})
     */
    private $forFemale=false;

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
    private $weight=0;
    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, precision=10, options={"default":0} )
     */
    private $length=0;
    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, precision=10, options={"default":0} )
     */
    private $height=0;
    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, precision=10, options={"default":0} )
     */
    private $width=0;

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
    private $prority;

    /**
     * @var int
     * @ORM\Column(type="integer", name="google_product_category_id", nullable=true)
     */
    private $googleProductCategoryId=0;


    /** @var string */
    private $extractedName; // not stored in database

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku(string $sku): void
    {
        $this->sku = $sku;
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
    public function setLastUpdate(DateTime $lastUpdate=null): void
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
    public function setParentSku(string $parentSku=null): void
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
    public function setInfoProvider(string $infoProvider=null): void
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
    public function setOriginCountryCode(string $originCountryCode=null): void
    {
        $this->originCountryCode = $originCountryCode;
    }

    /**
     * @return Classificator
     */
    public function getVendor(): ?Classificator
    {
        return $this->vendor;
    }

    /**
     * @param Classificator $vendor
     */
    public function setVendor(Classificator $vendor=null): void
    {
        $this->vendor = $vendor;
    }

    /**
     * @return Classificator
     */
    public function getManufacturer(): ?Classificator
    {
        return $this->manufacturer;
    }

    /**
     * @param Classificator $manufacturer
     */
    public function setManufacturer(Classificator $manufacturer=null): void
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return Classificator
     */
    public function getType(): ?Classificator
    {
        return $this->type;
    }

    /**
     * @param Classificator $type
     */
    public function setType(Classificator $type=null): void
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

    /**
     * @param Classificator $purpose
     */
    public function setPurpose(Classificator $purpose=null): void
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

    /**
     * @param Classificator $measure
     */
    public function setMeasure(Classificator $measure=null): void
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
    public function setColor(string $color=null): void
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
    public function setSize(string $size=null): void
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
    public function setPackSize(string $packSize=null): void
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
    public function setPackAmount(string $packAmount=null): void
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
    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
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
    public function setLength(float $length): void
    {
        $this->length = $length;
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
    public function setHeight(float $height): void
    {
        $this->height = $height;
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
    public function setWidth(float $width): void
    {
        $this->width = $width;
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
    public function setDeliveryTime(string $deliveryTime=null): void
    {
        $this->deliveryTime = $deliveryTime;
    }

    /**
     * @return Classificator
     */
    public function getBrand(): ?Classificator
    {
        return $this->brand;
    }

    /**
     * @param Classificator $brand
     */
    public function setBrand(Classificator $brand=null): void
    {
        $this->brand = $brand;
    }

    /**
     * @return Classificator
     */
    public function getLine(): ?Classificator
    {
        return $this->line;
    }

    /**
     * @param Classificator $line
     */
    public function setLine(Classificator $line): void
    {
        $this->line = $line;
    }

    /**
     * @param Classificator $classificator
     * @throws CatalogErrorException
     */
    public function setClassificator ( Classificator $classificator ) {
        $groupCode = $classificator->getGroup()->getCode();
        if ( array_search( $groupCode, self::CLASSIFICATORS_GROUPS) !== false ) {
            $setter = 'set'.$groupCode;
            $this->{$setter}($classificator);
        }
        else {
            throw new CatalogErrorException('Neteisingas klasifikatoriaus grupės kodas '.$groupCode);
        }
    }

    public static function lambdaGetSku ( Product $p ) {
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
    public function setDepositCode(string $depositCode=null): void
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
    public function setCodeFromCustom(string $codeFromCustom=null): void
    {
        $this->codeFromCustom = $codeFromCustom;
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
    public function setGuaranty(string $guaranty=null): void
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
    public function setCodeFromSupplier(string $codeFromSupplier=null): void
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
    public function setCodeFromVendor(string $codeFromVendor=null): void
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
    public function setProductGroup(Classificator $productgroup=null): void
    {
        $this->productgroup = $productgroup;
    }

    /**
     * @return string
     */
    public function getPrority(): ?string
    {
        return $this->prority;
    }

    /**
     * @param string $prority
     */
    public function setPrority(string $prority=null): void
    {
        $this->prority = $prority;
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
}