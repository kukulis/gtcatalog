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
    const ALLOWED_FIELDS = ['code', 'classificator_group', 'customs_code' ];

    /**
     * @var string
     * @ORM\Column(type="string", length=64, name="code")
     * @ORM\Id
     */
    private $code;

    /**
     * @var ClassificatorGroup
     * @ORM\ManyToOne(targetEntity="ClassificatorGroup" )
     * @ORM\JoinColumn(name="classificator_group", referencedColumnName="code")
     */
    private $classificatorGroup;

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="confirmed", nullable=true)
     */
    private $confirmed;

    // TODO out of architecture pattern?
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
    public function getClassificatorGroup(): ?ClassificatorGroup
    {
        return $this->classificatorGroup;
    }

    public function setClassificatorGroup(ClassificatorGroup $classificator_group): self
    {
        $this->classificatorGroup = $classificator_group;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code=null): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param Classificator $classificator
     * @return null|string
     */
    public static function lambdaGetCode ( Classificator $classificator ) {
        return $classificator->getCode();
    }

    public function getGroupCode() {
        if ( $this->classificatorGroup == null ) {
            return null;
        }
        else {
            return $this->classificatorGroup->getCode();
        }
    }

    /**
     * @param string $code
     * @param string $groupCode
     * @return Classificator
     */
    public static function createClassificator ( $code, $groupCode ) {
        if ( empty($code)) {
            return null;
        }
        $classificator = new Classificator();
        $classificator->setCode($code);
        $group = new ClassificatorGroup();
        $group->setCode($groupCode);
        $classificator->setClassificatorGroup($group);
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
    public function isConfirmed(): ?bool
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