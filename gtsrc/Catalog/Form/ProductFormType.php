<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.7.18
 * Time: 08.39
 */

namespace Gt\Catalog\Form;


use DateTime;
use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductLanguage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        /** @var ProductFormType $data */
//        $data = $options['data'];

        $builder
            ->add('p_sku', TextType::class, ['disabled' => true, 'label' => 'sku'])
            ->add('p_last_update', DateTimeType::class, [
                'disabled' => true,
                'label' => 'updated',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd HH:mm:ss',
            ])
            ->add('p_version', TextType::class, ['required' => false, 'disabled' => true, 'label' => 'version'])
            ->add('p_brand', TextType::class, ['label' => 'Brand'])
            ->add('p_line', TextType::class, ['label' => 'Line'])
            ->add('p_parent_sku', TextType::class, ['required' => false, 'label' => 'Parent'])
            ->add('p_info_provider', TextType::class, ['disabled' => true, 'label' => 'Info provider'])
            ->add('p_origin_country_code', TextType::class, ['required' => false, 'label' => 'Origin country code'])
            ->add('p_vendor', TextType::class, ['required' => false, 'label' => 'Vendor'])
            ->add('p_manufacturer', TextType::class, ['required' => false, 'label' => 'Manufacturer'])
            ->add('p_type', TextType::class, ['required' => false, 'label' => 'Type code'])
            ->add('p_purpose', TextType::class, ['required' => false, 'label' => 'Purpose code'])
            ->add('p_measure', TextType::class, ['required' => false, 'label' => 'Measure code'])
            ->add('p_color', TextType::class, ['required' => false, 'label' => 'Color'])
            ->add('p_for_male', CheckboxType::class, ['required' => false, 'label' => 'For male'])
            ->add('p_for_female', CheckboxType::class, ['required' => false, 'label' => 'For female'])
            ->add('p_size', TextType::class, ['required' => false, 'label' => 'Size'])
            ->add('p_pack_size', TextType::class, ['required' => false, 'label' => 'Pack Size'])
            ->add('p_pack_amount', TextType::class, ['required' => false, 'label' => 'Pack amount'])
            ->add('p_weight', NumberType::class, ['required' => false, 'label' => 'Weight'])
            ->add('p_length', NumberType::class, ['required' => false, 'label' => 'Length'])
            ->add('p_height', NumberType::class, ['required' => false, 'label' => 'Height'])
            ->add('p_width', NumberType::class, ['required' => false, 'label' => 'Width'])
            ->add('p_delivery_time', TextType::class, ['required' => false, 'label' => 'Delivery time'])
            ->add('p_google_category_id', TextType::class, ['required' => false, 'label' => 'Google category id'])
            ->add('p_priority', TextType::class, ['required' => false, 'label' => 'Priority'])
            ->add('p_ingredients', TextType::class, ['required' => false, 'label' => 'Ingredients'])
            ->add('p_code_from_custom', TextType::class, ['required' => false, 'label' => 'Customs code'])
            ->add('pl_language', TextType::class, ['disabled' => true, 'label' => 'Language'])
            ->add('pl_name', TextType::class, ['label' => 'Name'])
            ->add('pl_description', TextType::class, ['required' => false, 'label' => 'Description'])
            ->add('pl_label', TextType::class, ['required' => false, 'label' => 'Label'])
            ->add('pl_label_size', TextType::class, ['required' => false, 'label' => 'Label size'])
            ->add('p_label_material', TextType::class, ['required' => false, 'label' => 'Label material'])
            ->add('pl_variant_name', TextType::class, ['required' => false, 'label' => 'Variant name'])
            ->add('pl_info_provider', TextType::class, ['required' => false, 'label' => 'Info provider'])
            ->add('pl_tags', TextType::class, ['required' => false, 'label' => 'Tags'])
        ;
        $builder->add('save', SubmitType::class, ['label' => 'Save']);
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
     * @return DateTime
     */
    public function getPLastUpdate(): DateTime
    {
        return $this->product->getLastUpdate();
    }

    /**
     * @param DateTime $lastUpdate
     */
    public function setPLastUpdate(DateTime $lastUpdate): void
    {
        $this->product->setLastUpdate($lastUpdate);
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
    public function getPBrand(): ?string
    {
        return $this->product->getBrand();
    }

    /**
     * @param string $brandCode
     */
    public function setPBrand(string $brandCode = null): void
    {
        $this->product->setBrand($brandCode);
    }

    /**
     * @return string
     */
    public function getPLine(): ?string
    {
        return $this->product->getLine();
    }

    /**
     * @param string $lineCode
     */
    public function setPLine(string $lineCode = null): void
    {
        $this->product->setLine($lineCode);
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
    public function setPParentSku(string $parentSku = null): void
    {
        $this->product->setParentSku($parentSku);
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
    public function setPInfoProvider(string $infoProvider = null): void
    {
        $this->product->setInfoProvider($infoProvider);
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
    public function setPOriginCountryCode(string $originCountryCode = null): void
    {
        $this->product->setOriginCountryCode($originCountryCode);
    }

    public function getPVendor(): ?string
    {
        return $this->product->getVendor();
    }

    public function setPVendor($code)
    {
        $this->product->setVendor($code);
    }

    /**
     * @return string
     */
    public function getPManufacturer(): ?string
    {
        return $this->product->getManufacturer();
    }

    public function setPManufacturer(string $code = null): void
    {
        $this->product->setManufacturer($code);
    }

    public function getPType(): ?string
    {
        if ($this->product->getType() == null) {
            return null;
        }

        return $this->product->getType()->getCode();
    }

    public function setPType(string $type = null): void
    {
        if (empty($type)) {
            $this->product->setType(null);
            return;
        }
        $this->product->setType(Classificator::createClassificator($type, 'type'));
    }

    public function getPPurpose(): ?string
    {
        if ($this->product->getPurpose() == null) {
            return null;
        }
        return $this->product->getPurpose()->getCode();
    }

    /**
     * @param string $purpose
     */
    public function setPPurpose(string $purpose = null): void
    {
        if (empty($purpose)) {
            $this->product->setPurpose(null);
            return;
        }
        $this->product->setPurpose(Classificator::createClassificator($purpose, 'purpose'));
    }

    public function getPMeasure(): ?string
    {
        if ($this->product->getMeasure() == null) {
            return null;
        }
        return $this->product->getMeasure()->getCode();
    }

    /**
     * @param string $measure
     */
    public function setPMeasure(string $measure = null): void
    {
        if (empty($measure)) {
            $this->product->setMeasure(null);
            return;
        }

        $this->product->setMeasure(Classificator::createClassificator($measure, 'measure'));
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
    public function setPColor(string $color = null): void
    {
        $this->product->setColor($color);
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
        $this->product->setForMale($forMale);
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
        $this->product->setForFemale($forFemale);
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
    public function setPSize(string $size = null): void
    {
        $this->product->setSize($size);
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
    public function setPPackSize(string $packSize = null): void
    {
        $this->product->setPackSize($packSize);
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
    public function setPPackAmount(string $packAmount = null): void
    {
        $this->product->setPackAmount($packAmount);
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
        $this->product->setWeight($weight);
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
        $this->product->setLength($length);
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
        $this->product->setHeight($height);
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
        $this->product->setWidth($width);
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
    public function setPDeliveryTime(string $deliveryTime = null): void
    {
        $this->product->setDeliveryTime($deliveryTime);
    }

    /**
     * @return int
     */
    public function getPGoogleCategoryId(): ?int
    {
        return $this->product->getGoogleProductCategoryId();
    }

    /**
     * @param int $googleProductCategoryId
     */
    public function setPGoogleCategoryId(int $googleProductCategoryId): void
    {
        $this->product->setGoogleProductCategoryId($googleProductCategoryId);
    }

    public function getPPriority()
    {
        return $this->product->getPriority();
    }

    public function setPPriority($priority)
    {
        $this->product->setPriority($priority);
    }

    public function getPIngredients()
    {
        return $this->product->getIngredients();
    }

    public function setPIngredients($ingredients)
    {
        $this->product->setIngredients($ingredients);
    }

    public function getPCodeFromCustom() {
        return $this->product->getCodeFromCustom();
    }
    public function setPCodeFromCustom($code) {
        $this->product->setCodeFromCustom($code);
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
    public function setPLName(string $name = null): void
    {
        $this->productLanguage->setName($name);
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
    public function setPLDescription(string $description = null): void
    {
        $this->productLanguage->setDescription($description);
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
    public function setPLLabel(string $label = null): void
    {
        $this->productLanguage->setLabel($label);
    }

    /**
     * @return string
     */
    public function getPLLabelSize(): ?string
    {
        return $this->productLanguage->getLabelSize();
    }

    /**
     * @param string $label
     */
    public function setPLLabelSize(string $labelSize = null): void
    {
        $this->productLanguage->setLabelSize($labelSize);
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
    public function setPLInfoProvider(string $infoProvider = null): void
    {
        $this->productLanguage->setInfoProvider($infoProvider);
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
    public function setPLVariantName(string $variantName = null): void
    {
        $this->productLanguage->setVariantName($variantName);
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
    public function setPLTags(string $tags = null): void
    {
        $this->productLanguage->setTags($tags);
    }

    /**
     * @return string
     */
    public function getPlLanguage()
    {
        return $this->productLanguage->getLanguage()->getCode();
    }

    public function getPLabelMaterial() {
        return $this->product->getLabelMaterial();
    }

    public function setPLabelMaterial($m) {
        $this->product->setLabelMaterial($m);
    }

}