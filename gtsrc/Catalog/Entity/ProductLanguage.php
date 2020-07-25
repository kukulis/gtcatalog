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
    /**
     * @var Product
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product" )
     * @ORM\JoinColumn(name="sku", referencedColumnName="sku")
     */
    private $product;

    /**
     * @var Language
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Language" )
     * @ORM\JoinColumn(name="language_code", referencedColumnName="code")
     */
    private $language;


    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $label;


    /**
     * @var string
     * @ORM\Column(type="string", name="variant_name")
     */
    private $variantName;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    private $infoProvider;


    /**
     * @var string
     * @ORM\Column(type="string", name="tags")
     */
    private $tags;

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
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language): void
    {
        $this->language = $language;
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
    public function setName(string $name=null): void
    {
        $this->name = $name;
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
    public function setDescription(string $description=null): void
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
    public function setLabel(string $label=null): void
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
    public function setInfoProvider(string $infoProvider=null): void
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
    public function setVariantName(string $variantName=null): void
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
    public function setTags(string $tags=null): void
    {
        $this->tags = $tags;
    }

}