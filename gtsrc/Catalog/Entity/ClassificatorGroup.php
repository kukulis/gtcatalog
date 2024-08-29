<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.7.11
 * Time: 19.15
 */

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="classificators_groups")
 */
class ClassificatorGroup
{
    /**
     * @var string
     * @ORM\Column(type="string", length=64, name="code")
     * @ORM\Id
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(type="string", name="name")
     */
    private $name;

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

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

    public static function create($code) {
        $g = new ClassificatorGroup();
        $g->setCode($code);
        return $g;
    }

}