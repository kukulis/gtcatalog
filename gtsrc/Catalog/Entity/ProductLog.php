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
    private $productNew;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $productOld;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $productLanguageNew;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $productLanguageOld;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User" )
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", default="0")
     *
     */
    private $deleted;

    /**
     * @ORM\Column(type="string", length=255)
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

    /**
     * @return bool|null
     */
    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    /**
     * @param bool|null $deleted
     */
    public function setDeleted(?bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUsername(): string
    {
        return $this->user->getName();
    }

    public function setProductLanguage($productLanguageNew)
    {
        $this->productLanguageNew = $productLanguageNew;
    }

    public function getProductLanguage()
    {
        return $this->productLanguageNew;
    }

    public function setProductLanguageOld($productLanguageOld)
    {
        $this->productLanguageOld = $productLanguageOld;
    }

    public function getProductLanguageOld()
    {
        return $this->productLanguageOld;
    }

    public function getProductDiff()
    {
        $array2 = json_decode($this->getProductNew(), true);
        $array1 = json_decode($this->getProductOld(), true);

        return $this->compareArrays($array1, $array2);
    }

    public function getLanguageDiff()
    {
        $array2 = json_decode($this->getProductLanguage(), true);
        $array1 = json_decode($this->getProductLanguageOld(), true);

        return $this->compareArrays($array1, $array2);
    }

    function compareArrays($array1, $array2) {
        $differences = [];

        foreach ($array1 as $key => $value) {
            if (array_key_exists($key, $array2)) {
                if (is_array($value) && is_array($array2[$key])) {
                    $subDiff = $this->compareArrays($value, $array2[$key]);
                    if (!empty($subDiff)) {
                        $differences[$key] = $subDiff;
                    }
                } elseif ($value !== $array2[$key]) {
                    if (!empty($value)) {
                        $differences[$key] = [
                            'old' => $value,
                            'new' => $array2[$key]
                        ];
                    }
                }
            } else {
                $differences[$key] = [
                    'old' => $value,
                    'new' => 'removed'
                ];
            }
        }

        foreach ($array2 as $key => $value) {
            if (!array_key_exists($key, $array1)) {
                $differences[$key] = [
                    'old' => 'added',
                    'new' => $value
                ];
            }
        }

        $result = array_filter($differences, function($diff) {
            return !empty($diff);
        });

        return json_encode($result);
    }
}
