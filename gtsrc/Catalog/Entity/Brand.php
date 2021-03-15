<?php
/**
 * Brand.php
 * Created by Giedrius Tumelis.
 * Date: 2021-03-15
 * Time: 10:46
 */

namespace Gt\Catalog\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass=Gt\Catalog\Repository\BrandsRepository::class )
 * @ORM\Table(name="brands")
 */
class Brand
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $brand;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     */
    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }
}