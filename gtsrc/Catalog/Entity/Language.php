<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 11.38
 */

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="languages")
 */
class Language
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
     * @return string
     */
    public function getCode(): string
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
     * @return string
     */
    public function getName(): string
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
    public function getLocaleCode(): string
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

    /**
     * @var string
     * @ORM\Column(type="string", length=64 )
     */
    private $localeCode;



}