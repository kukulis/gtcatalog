<?php

namespace Gt\Catalog\DTO;

class ProductLanguageLogDto
{
    /**
     * @var mixed
     */
    private $name;
    /**
     * @var mixed
     */
    private $shortDescription;
    /**
     * @var mixed
     */
    private $description;
    /**
     * @var mixed
     */
    private $label;
    /**
     * @var mixed
     */
    private $variantName;
    /**
     * @var mixed
     */
    private $infoProvider;
    /**
     * @var mixed
     */
    private $tags;
    /**
     * @var mixed
     */
    private $labelSize;
    /**
     * @var mixed
     */
    private $distributor;
    /**
     * @var mixed
     */
    private $composition;
    /**
     * @var mixed
     */
    private $labelMaterial;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getVariantName()
    {
        return $this->variantName;
    }

    public function setVariantName($variantName)
    {
        $this->variantName = $variantName;
    }
    
    public function getInfoProvider()
    {
        return $this->infoProvider;
    }
    
    public function setInfoProvider($infoProvider)
    {
        $this->infoProvider = $infoProvider;
    }
    
    public function getTags()
    {
        return $this->tags;
    }
    
    public function setTags($tags)
    {
        $this->tags = $tags;
    }
    
    public function getLabelSize()
    {
        return $this->labelSize;
    }
    
    public function setLabelSize($labelSize)
    {
        $this->labelSize = $labelSize;
    }
    
    public function getDistributor()
    {
        return $this->distributor;
    }
    
    public function setDistributor($distributor)
    {
        $this->distributor = $distributor;
    }
    public function getComposition()
    {
        return $this->composition;
    }
    
    public function setComposition($composition)
    {
        $this->composition = $composition;
    }

    public function getLabelMaterial()
    {
        return $this->labelMaterial;
    }

    public function setLabelMaterial($labelMaterial)
    {
        $this->labelMaterial = $labelMaterial;
    }
}