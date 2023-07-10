<?php

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity
 * @ORM\Table(name="languages")
 */
class Language implements JsonSerializable
{

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=2)
     */
    private $code;


    /**
     * @var string
     * @ORM\Column(type="string", length=64 )
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=64 )
     */
    private $localeCode;


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
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    /**
     * @param string $localeCode
     */
    public function setLocaleCode(string $localeCode): void
    {
        $this->localeCode = $localeCode;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'locale_code' => $this->localeCode,
        ];
    }
}