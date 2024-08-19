<?php

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=Gt\Catalog\Repository\PackageTypeRepository::class)
 * @ORM\Table(name="packages_types")
 */
class PackageType
{
    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     * @ORM\Id
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): PackageType
    {
        $this->code = $code;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): PackageType
    {
        $this->description = $description;
        return $this;
    }

}