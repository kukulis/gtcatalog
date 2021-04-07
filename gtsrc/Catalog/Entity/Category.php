<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 11.45
 */

namespace Gt\Catalog\Entity;


use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class Category
{
    const ALLOWED_FIELDS = ['code', 'parent', 'customs_code' ];

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
     * @var bool
     * @ORM\Column(type="boolean", name="confirmed", nullable=true)
     */
    private $confirmed;

    /**
     * @var string
     * @ORM\Column(type="string", name="customs_code", length=16, nullable=true)
     */
    private $customsCode;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", name="date_created", nullable=true, options={"default":"CURRENT_TIMESTAMP"})
     *
     */
    private $dateCreated;

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

    /**
     * @param Category $category
     * @return string|null
     */
    public static function lambdaGetCode ( Category $category ) {
        return $category->getCode();
    }

    /**
     * @return string|null
     */
    public function getParentCode() {
        if ( $this->parent == null ) {
            return null;
        }
        return $this->parent->getCode();
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        if ( $this->confirmed === null ) {
            return false;
        }
        return $this->confirmed;
    }

    /**
     * @param bool $confirmed
     */
    public function setConfirmed(bool $confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @return string
     */
    public function getCustomsCode()
    {
        return $this->customsCode;
    }

    /**
     * @param string $customsCode
     */
    public function setCustomsCode(string $customsCode=null)
    {
        $this->customsCode = $customsCode;
    }

    /**
     * @return DateTime
     */
    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param DateTime $dateCreated
     */
    public function setDateCreated(DateTime $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }
}