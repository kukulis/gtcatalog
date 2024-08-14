<?php

namespace Gt\Catalog\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gt\Catalog\Repository\ProductLogRepository;

/**
 * @ORM\Entity(repositoryClass=ProductLogRepository::class)
 */
class ProductLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $language;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $productOld;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $productNew;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User" )
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     *
     */
    private $sku;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", name="date_created", nullable=true, options={"default":"CURRENT_TIMESTAMP"})
     *
     */
    private DateTime $dateCreated;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param string|null $language
     * @return ProductLog
     */
    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
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
     * @return void
     */
    public function setDateCreated(DateTime $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return mixed
     */
    public function getProductOld()
    {
        return $this->productOld;
    }

    /**
     * @param mixed $productOld
     */
    public function setProductOld($productOld): void
    {
        $this->productOld = $productOld;
    }

    /**
     * @return mixed
     */
    public function getProductNew()
    {
        return $this->productNew;
    }

    /**
     * @param mixed $productNew
     */
    public function setProductNew($productNew): void
    {
        $this->productNew = $productNew;
    }

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param mixed $sku
     */
    public function setSku($sku): void
    {
        $this->sku = $sku;
    }


    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }
}
