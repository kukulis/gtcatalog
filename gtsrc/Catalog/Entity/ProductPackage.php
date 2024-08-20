<?php

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="products_packages",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_products_packages",columns={"sku", "type_code"})} )
 *
 */
class ProductPackage
{

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    private $id;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="Product" )
     * @ORM\JoinColumn(name="sku", referencedColumnName="sku")
     */
    private $product;


    /**
     * @var PackageType
     * @ORM\ManyToOne(targetEntity="PackageType" )
     * @ORM\JoinColumn(name="type_code", referencedColumnName="code")
     */
    private $packageType;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, precision=10, options={"default":0} )
     */
    private $weight;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ProductPackage
    {
        $this->id = $id;
        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): ProductPackage
    {
        $this->product = $product;
        return $this;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): ProductPackage
    {
        $this->weight = $weight;
        return $this;
    }

    public function getPackageType(): PackageType
    {
        return $this->packageType;
    }

    public function setPackageType(PackageType $packageType): ProductPackage
    {
        $this->packageType = $packageType;
        return $this;
    }


}