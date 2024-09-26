<?php

namespace Gt\Catalog\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gt\Catalog\Repository\ProductLogRepository;

/**
 * @ORM\Entity(repositoryClass=ProductLogRepository::class)
 * @ORM\Table(name="product_log",
 * indexes={
 *     @ORM\Index(name="idx_user_id", columns={"id"})
 *         }
 * )
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
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     *
     */
    private $deleted=0;

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
        $product = json_decode($this->getProductNew(), true);
        $ProductOld = json_decode($this->getProductOld(), true);

        return $this->compareJsonRecursive($ProductOld, $product);
    }

    public function getLanguageDiff()
    {
        $productLanguage = json_decode($this->getProductLanguage(), true);
        $productLanguageOld = json_decode($this->getProductLanguageOld(), true);

        return $this->compareJsonRecursive($productLanguageOld, $productLanguage);
    }

    function compareJsonRecursive(array $oldData, array $newData, string $path = '') {
        $changes = [];

        foreach ($newData as $key => $newValue) {
            $currentPath = $path ? $path . '.' . $key : $key;

            if (!array_key_exists($key, $oldData)) {
                $changes[] = [
                    'path' => $currentPath,
                    'added' => $newValue
                ];
            } else {
                $oldValue = $oldData[$key];

                if (is_array($newValue) && is_array($oldValue)) {
                    $nestedChanges = $this->compareJsonRecursive($oldValue, $newValue, $currentPath);
                    $changes = array_merge($changes, (array)$nestedChanges);
                }
                elseif ($newValue !== $oldValue) {
                    $changes[] = [
                        'path' => $currentPath,
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
        }

        foreach ($oldData as $key => $oldValue) {
            $currentPath = $path ? $path . '.' . $key : $key;

            if (!array_key_exists($key, $newData)) {
                $changes[] = [
                    'path' => $currentPath,
                    'removed' => $oldValue
                ];
            }
        }

        return json_encode($changes);
    }
}
