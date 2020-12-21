<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.7.11
 * Time: 18.53
 */

namespace Gt\Catalog\Entity;


use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="classificators")
 */
class Classificator
{
    const ALLOWED_FIELDS = ['code', 'group', 'customs_code' ];

    /**
     * @var string
     * @ORM\Column(type="string", length=64, name="code")
     * @ORM\Id
     */
    private $code;

    /**
     * @var ClassificatorGroup
     * @ORM\ManyToOne(targetEntity="ClassificatorGroup" )
     * @ORM\JoinColumn(name="group", referencedColumnName="code")
     */
    private $group;

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="confirmed", nullable=true)
     */
    private $confirmed;

    /**
     * @var string
     * @ORM\Column(type="string", name="customs_code", length=16, nullable=true)
     */
    private $customsCode;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", name="date_created", nullable=true, options={"default":"CURRENT_TIMESTAMP"})
     *
     */
    private $dateCreated;


    private $assignedValue; // not stored in db

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

    /**
     * @return mixed
     */
    public function getAssignedValue()
    {
        return $this->assignedValue;
    }

    /**
     * @param mixed $assignedValue
     */
    public function setAssignedValue($assignedValue): void
    {
        $this->assignedValue = $assignedValue;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    /**
     * @param bool $confirmed
     */
    public function setConfirmed(bool $confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @return string
     */
    public function getCustomsCode(): ?string
    {
        return $this->customsCode;
    }

    /**
     * @param string $customsCode
     */
    public function setCustomsCode(string $customsCode=null): void
    {
        $this->customsCode = $customsCode;
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
     */
    public function setDateCreated(DateTime $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }
}