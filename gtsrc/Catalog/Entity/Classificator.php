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
     * @var string
     * @ORM\Column(type="string", length=64, name="code")
     * @ORM\Id
     */
    private $code;

    /**
     * @var ClassificatorGroup
     * @ORM\ManyToOne(targetEntity="ClassificatorGroup" )
     * @ORM\JoinColumn(name="group_code", referencedColumnName="code")
     */
    private $group;


    /**
     * @return ClassificatorGroup
     */
    public function getGroup(): ?ClassificatorGroup
    {
        return $this->group;
    }

    /**
     * @param ClassificatorGroup $group
     */
    public function setGroup(ClassificatorGroup $group): void
    {
        $this->group = $group;
    }

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
    public function setCode(string $code=null): void
    {
        $this->code = $code;
    }

    /**
     * @param Classificator $classificator
     * @return null|string
     */
    public static function lambdaGetCode ( Classificator $classificator ) {
        return $classificator->getCode();
    }

    public function getGroupCode() {
        if ( $this->group == null ) {
            return null;
        }
        else {
            return $this->group->getCode();
        }
    }

    /**
     * @param string $code
     * @param string $groupCode
     * @return Classificator
     */
    public static function createClassificator ( $code, $groupCode ) {
        $classificator = new Classificator();
        $classificator->setCode($code);
        $group = new ClassificatorGroup();
        $group->setCode($groupCode);
        $classificator->setGroup($group);
        return $classificator;
    }

}