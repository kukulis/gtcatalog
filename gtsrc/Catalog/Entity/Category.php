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
 * @ORM\Table(name="categories")
 */
class Category
{
    const ALLOWED_FIELDS = ['code', 'parent' ];

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     * @ORM\Id
     */
    private $code;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category" )
     * @ORM\JoinColumn(name="parent", referencedColumnName="code")
     */
    private $parent;

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return Category
     */
    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * @param Category $parent
     */
    public function setParent(Category $parent=null): void
    {
        $this->parent = $parent;
    }

    /**
     * @param $code
     * @return Category
     */
    public static function createCategory ( $code ) {
        $category = new Category();
        $category->setCode($code);
        return $category;
    }

    public function lambdaGetCode ( Category $category ) {
        return $category->getCode();
    }

    public function getParentCode() {
        if ( $this->parent == null ) {
            return null;
        }
        return $this->parent->getCode();
    }
}