<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 11.29
 */

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="products")
 */
class Product
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=32)
     */
    private $sku;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     *
     */
    private $lastUpdate;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdate(): \DateTime
    {
        return $this->lastUpdate;
    }

    /**
     * @param \DateTime $lastUpdate
     */
    public function setLastUpdate(\DateTime $lastUpdate): void
    {
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion(int $version): void
    {
        $this->version = $version;
    }
}