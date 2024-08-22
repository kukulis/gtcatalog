<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 11.43
 */

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="products_languages")
 */
class ProductLanguage
{
    const ALLOWED_FIELDS = [
        'name',
        'short_description',
        'description',
        'label',
        'label_size', // TODO not translatable
        'variant_name',
        'info_provider',
        'tags',
    ];

    /**
     * @var Product
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product" )
     * @ORM\JoinColumn(name="sku", referencedColumnName="sku")
     */
    private $product;

    /**
     * @var ?Language
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Language" )
     * @ORM\JoinColumn(name="language", referencedColumnName="code")
     */
    private $language;


    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $shortDescription;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $label;


    /**
     * @var string
     * @ORM\Column(type="string", name="variant_name", nullable=true)
     */
    private $variantName;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $infoProvider;


    /**
     * @var string
     * @ORM\Column(type="string", name="tags", nullable=true)
     */
    private $tags;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, name="label_size", nullable=true)
     */
    private $labelSize;


    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $distributor;


    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $composition;

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->product->getSku();
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Language
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name = null): self
    {
        $this->name = $name;

        return $this;
    }


    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription = null): void
    {
        $this->shortDescription = $shortDescription;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description = null): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label = null): void
    {
        $this->label = $label;
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
    public function getVariantName(): ?string
    {
        return $this->variantName;
    }

    /**
     * @param string $variantName
     */
    public function setVariantName(string $variantName = null): void
    {
        $this->variantName = $variantName;
    }

    /**
     * @return string
     */
    public function getTags(): ?string
    {
        return $this->tags;
    }

    /**
     * @param string $tags
     */
    public function setTags(string $tags = null): void
    {
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getLabelSize(): ?string
    {
        return $this->labelSize;
    }

    /**
     * @param string $labelSize
     */
    public function setLabelSize(string $labelSize = null): void
    {
        $this->labelSize = $labelSize;
    }

    /**
     * @return string
     */
    public function getDistributor(): ?string
    {
        return $this->distributor;
    }

    /**
     * @param string $distributor
     */
    public function setDistributor(string $distributor = null): void
    {
        $this->distributor = $distributor;
    }

    /**
     * @return string
     */
    public function getComposition(): ?string
    {
        return $this->composition;
    }

    /**
     * @param string $composition
     */
    public function setComposition(string $composition = null): void
    {
        $this->composition = $composition;
    }

    public static function lambdaGetProduct(ProductLanguage $productLanguage)
    {
        return $productLanguage->getProduct();
    }

    public function getLanguageCode(): ?string
    {
        if (is_object($this->language)) {
            return $this->language->getCode();
        }

        return null;
    }

    public function getTagsArray(): array
    {
        if ($this->tags == null) {
            return [];
        }

        return array_map('trim', explode(',', $this->tags));
    }

}