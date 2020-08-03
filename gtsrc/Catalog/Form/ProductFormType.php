<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.7.18
 * Time: 08.39
 */

namespace Gt\Catalog\Form;


use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\Language;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductLanguage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * Class ProductFormType
 * @package Gt\Catalog\Form
 */
class ProductFormType extends AbstractType
{
    /** @var Product */
    private $product;

    /** @var ProductLanguage */
    private $productLanguage;

    /** @var Language[] */
    private $languages;

    /** @var string */
    private $selectedLanguage;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductFormType $data */
        $data = $options['data'];

        $builder
            ->add('p_sku'                   , TextType::class, ['disabled'=>true] )
            ->add('p_last_update'           , DateTimeType::class, ['required'=>false])
            ->add('p_version'               , TextType::class, ['required'=>false] )
            ->add('p_brand_code'            , TextType::class )
            ->add('p_line_code'             , TextType::class )
            ->add('p_parent_sku'            , TextType::class, ['required'=>false] )
            ->add('p_info_provider'         , TextType::class, ['disabled'=>true] )
            ->add('p_origin_country_code'   , TextType::class, ['required'=>false] )
            ->add('p_vendor'           , TextType::class, ['required'=>false] )
            ->add('p_manufacturer'     , TextType::class, ['required'=>false] )
            ->add('p_type'             , TextType::class, ['required'=>false] )
            ->add('p_measure'          , TextType::class, ['required'=>false] )
            ->add('p_color'                 , TextType::class, ['required'=>false] )
            ->add('p_for_male'              , CheckboxType::class, ['required'=>false] )
            ->add('p_for_female'            , CheckboxType::class, ['required'=>false] )
            ->add('p_size'                  , TextType::class, ['required'=>false] )
            ->add('p_pack_size'             , TextType::class, ['required'=>false] )
            ->add('p_pack_amount'           , TextType::class, ['required'=>false] )
            ->add('p_weight'                , NumberType::class, ['required'=>false] )
            ->add('p_length'                , NumberType::class, ['required'=>false] )
            ->add('p_height'                , NumberType::class, ['required'=>false] )
            ->add('p_width'                 , NumberType::class, ['required'=>false] )
            ->add('p_delivery_time'         , TextType::class, ['required'=>false] )
            ->add('selectedLanguage', ChoiceType::class, [
                'choices' => ['a'=>'a', 'b'=>'b']
            ] ) // TODO languages from options->data
                ->add('select_language', SubmitType::class )
                ->add('add_language', SubmitType::class )
            ->add('pl_name'                 , TextType::class )
            ->add('pl_description'          , TextType::class, ['required'=>false] )
            ->add('pl_label'                , TextType::class, ['required'=>false] )
            ->add('pl_variant_name'         , TextType::class, ['required'=>false] )
            ->add('pl_info_provider'        , TextType::class, ['required'=>false] )
            ->add('pl_tags'                 , TextType::class, ['required'=>false] );

            $builder->add('save', SubmitType::class, ['label' => 'Išsaugoti']);

    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return ProductLanguage
     */
    public function getProductLanguage(): ProductLanguage
    {
        return $this->productLanguage;
    }

    /**
     * @param ProductLanguage $productLanguage
     */
    public function setProductLanguage(ProductLanguage $productLanguage): void
    {
        $this->productLanguage = $productLanguage;
    }

    /**
     * @return Language[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * @param Language[] $languages
     */
    public function setLanguages(array $languages): void
    {
        $this->languages = $languages;
    }

    /**
     * @return string
     */
    public function getSelectedLanguage(): string
    {
        return $this->selectedLanguage;
    }

    /**
     * @param string $selectedLanguage
     */
    public function setSelectedLanguage(string $selectedLanguage): void
    {
        $this->selectedLanguage = $selectedLanguage;
    }


    // ========================================
    // delegating
    // ========================================


    /**
     * @return string
     */
    public function getPSku(): string
    {
        return $this->product->getSku();
    }

    /**
     * @param string $sku
     */
    public function setPSku(string $sku): void
    {
        $this->product->setSku($sku);
    }

    /**
     * @return \DateTime
     */
    public function getPLastUpdate(): \DateTime
    {
        return $this->product->getLastUpdate();
    }

    /**
     * @param \DateTime $lastUpdate
     */
    public function setPLastUpdate(\DateTime $lastUpdate): void
    {
        $this->product->setLastUpdate( $lastUpdate );
    }

    /**
     * @return int
     */
    public function getPVersion(): int
    {
        return $this->product->getVersion();
    }

    /**
     * @param int $version
     */
    public function setPVersion(int $version): void
    {
        $this->product->setVersion($version);
    }

    /**
     * @return string
     */
    public function getPBrandCode(): ?string
    {
        if ( $this->product->getBrand() == null ) {
            return '';
        }
        return $this->product->getBrand()->getCode();
    }

    /**
     * @param string $brandCode
     */
    public function setPBrandCode(string $brandCode=null): void
    {
        if ( empty($brandCode) ) {
            $this->product->setBrand(null);
            return;
        }
        if ( $this->product->getBrand() == null ) {
            $this->product->setBrand(new Classificator());
        }

        $this->product->getBrand()->setCode($brandCode);
    }

    /**
     * @return string
     */
    public function getPLineCode(): ?string
    {
        if ( $this->product->getLine()== null ) {
            return '';
        }
        return $this->product->getLine()->getCode();
    }

    /**
     * @param string $lineCode
     */
    public function setPLineCode(string $lineCode=null): void
    {
        if ( empty($lineCode)) {
            $this->product->setLine(null);
            return ;
        }
        if ( $this->product->getLine()== null ) {
            $this->product->setLine(new Classificator());
        }
        $this->product->getLine()->setCode($lineCode);
    }

    /**
     * @return string
     */
    public function getPParentSku(): ?string
    {
        return $this->product->getParentSku();
    }

    /**
     * @param string $parentSku
     */
    public function setPParentSku(string $parentSku=null): void
    {
        $this->product->setParentSku( $parentSku );
    }

    /**
     * @return string
     */
    public function getPInfoProvider(): ?string
    {
        return $this->product->getInfoProvider();
    }

    /**
     * @param string $infoProvider
     */
    public function setPInfoProvider(string $infoProvider=null): void
    {
        $this->product->setInfoProvider( $infoProvider );
    }

    /**
     * @return string
     */
    public function getPOriginCountryCode(): ?string
    {
        return $this->product->getOriginCountryCode();
    }

    /**
     * @param string $originCountryCode
     */
    public function setPOriginCountryCode(string $originCountryCode=null): void
    {
        $this->product->setOriginCountryCode( $originCountryCode );
    }

    /**
     * @return Classificator
     */
    public function getPVendor(): ?Classificator
    {
        return $this->product->getVendor();
    }

    /**
     * @param Classificator $vendor
     */
    public function setPVendor(Classificator $vendor=null): void
    {
        $this->product->setVendor( $vendor );
    }

    /**
     * @return Classificator
     */
    public function getPManufacturer(): ?Classificator
    {
        return $this->product->getManufacturer();
    }

    /**
     * @param Classificator $manufacturer
     */
    public function setPManufacturer(Classificator $manufacturer=null): void
    {
        $this->product->setManufacturer( $manufacturer );
    }

    /**
     * @return Classificator
     */
    public function getPType(): ?Classificator
    {
        return $this->product->getType();
    }

    /**
     * @param Classificator $type
     */
    public function setPType(Classificator $type=null): void
    {
        $this->product->setType($type);
    }

    /**
     * @return Classificator
     */
    public function getPPurpose(): ?Classificator
    {
        return $this->product->getPurpose();
    }

    /**
     * @param Classificator $purpose
     */
    public function setPPurpose(Classificator $purpose=null): void
    {
        $this->product->setPurpose( $purpose );
    }

    /**
     * @return Classificator
     */
    public function getPMeasure(): ?Classificator
    {
        return $this->product->getMeasure();
    }

    /**
     * @param Classificator $measure
     */
    public function setPMeasure(Classificator $measure=null): void
    {
        $this->product->setMeasure( $measure );
    }

    /**
     * @return string
     */
    public function getPColor(): ?string
    {
        return $this->product->getColor();
    }

    /**
     * @param string $color
     */
    public function setPColor(string $color=null): void
    {
        $this->product->setColor( $color );
    }

    /**
     * @return bool
     */
    public function isPForMale(): bool
    {
        return $this->product->isForMale();
    }

    /**
     * @param bool $forMale
     */
    public function setPForMale(bool $forMale): void
    {
        $this->product->setForMale( $forMale );
    }

    /**
     * @return bool
     */
    public function isPForFemale(): bool
    {
        return $this->product->isForFemale();
    }

    /**
     * @param bool $forFemale
     */
    public function setPForFemale(bool $forFemale): void
    {
        $this->product->setForFemale( $forFemale );
    }

    /**
     * @return string
     */
    public function getPSize(): ?string
    {
        return $this->product->getSize();
    }

    /**
     * @param string $size
     */
    public function setPSize(string $size=null): void
    {
        $this->product->setSize( $size );
    }

    /**
     * @return string
     */
    public function getPPackSize(): ?string
    {
        return $this->product->getPackSize();
    }

    /**
     * @param string $packSize
     */
    public function setPPackSize(string $packSize=null): void
    {
        $this->product->setPackSize( $packSize );
    }

    /**
     * @return string
     */
    public function getPPackAmount(): ?string
    {
        return $this->product->getPackAmount();
    }

    /**
     * @param string $packAmount
     */
    public function setPPackAmount(string $packAmount=null): void
    {
        $this->product->setPackAmount( $packAmount );
    }

    /**
     * @return float
     */
    public function getPWeight(): float
    {
        return $this->product->getWeight();
    }

    /**
     * @param float $weight
     */
    public function setPWeight(float $weight): void
    {
        $this->product->setWeight( $weight );
    }

    /**
     * @return float
     */
    public function getPLength(): float
    {
        return $this->product->getLength();
    }

    /**
     * @param float $length
     */
    public function setPLength(float $length): void
    {
        $this->product->setLength( $length );
    }

    /**
     * @return float
     */
    public function getPHeight(): float
    {
        return $this->product->getHeight();
    }

    /**
     * @param float $height
     */
    public function setPHeight(float $height): void
    {
        $this->product->setHeight( $height );
    }

    /**
     * @return float
     */
    public function getPWidth(): float
    {
        return $this->product->getWidth();
    }

    /**
     * @param float $width
     */
    public function setPWidth(float $width): void
    {
        $this->product->setWidth( $width );
    }

    /**
     * @return string
     */
    public function getPDeliveryTime(): ?string
    {
        return $this->product->getDeliveryTime();
    }

    /**
     * @param string $deliveryTime
     */
    public function setPDeliveryTime(string $deliveryTime=null): void
    {
        $this->product->setDeliveryTime( $deliveryTime );
    }


    // delegating language

    /**
     * @return string
     */
    public function getPLName(): ?string
    {
        return $this->productLanguage->getName();
    }

    /**
     * @param string $name
     */
    public function setPLName(string $name=null): void
    {
        $this->productLanguage->setName( $name );
    }

    /**
     * @return string
     */
    public function getPLDescription(): ?string
    {
        return $this->productLanguage->getDescription();
    }

    /**
     * @param string $description
     */
    public function setPLDescription(string $description=null): void
    {
        $this->productLanguage->setDescription( $description );
    }

    /**
     * @return string
     */
    public function getPLLabel(): ?string
    {
        return $this->productLanguage->getLabel();
    }

    /**
     * @param string $label
     */
    public function setPLLabel(string $label=null): void
    {
        $this->productLanguage->setLabel( $label );
    }

    /**
     * @return string
     */
    public function getPLInfoProvider(): ?string
    {
        return $this->productLanguage->getInfoProvider();
    }

    /**
     * @param string $infoProvider
     */
    public function setPLInfoProvider(string $infoProvider=null): void
    {
        $this->productLanguage->setInfoProvider( $infoProvider );
    }

    /**
     * @return string
     */
    public function getPLVariantName(): ?string
    {
        return $this->productLanguage->getVariantName();
    }

    /**
     * @param string $variantName
     */
    public function setPLVariantName(string $variantName=null): void
    {
        $this->productLanguage->setVariantName( $variantName );
    }

    /**
     * @return string
     */
    public function getPLTags(): ?string
    {
        return $this->productLanguage->getTags();
    }

    /**
     * @param string $tags
     */
    public function setPLTags(string $tags=null): void
    {
        $this->productLanguage->setTags( $tags );
    }

}