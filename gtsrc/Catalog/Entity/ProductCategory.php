<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 11.45
 */

namespace Gt\Catalog\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="products_categories")
 */
class ProductCategory
{
    /**
     * @var Product
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product" )
     * @ORM\JoinColumn(name="sku", referencedColumnName="sku")
     */
    private $product;

    /**
     * @var Category
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Category" )
     * @ORM\JoinColumn(name="category", referencedColumnName="code")
     */
    private $category;


    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $priority;

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
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
}