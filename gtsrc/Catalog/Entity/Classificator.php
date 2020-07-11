<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.7.11
 * Time: 18.53
 */

namespace Gt\Catalog\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="classificators")
 */
class Classificator
{
    /**
     * @var ClassificatorGroup
     * @ORM\ManyToOne(targetEntity="ClassificatorGroup" )
     * @ORM\JoinColumn(name="group_code", referencedColumnName="code")
     */
    private $groupCode;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, name="code")
     * @ORM\Id
     */
    private $code;

    /**
     * @return string
     */
    public function getGroupCode(): string
    {
        return $this->groupCode;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}