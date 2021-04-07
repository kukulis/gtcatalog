<?php
/**
 * CustomsNumber.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-18
 * Time: 12:50
 */

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents structure of the customs codes from global customs codes database.
 * Not practicaly used in this project yet.
 *
 * @ORM\Entity
 * @ORM\Table(name="customs_numbers")
 */
class CustomsNumber
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, unique=true)
     */
    private $sortingCode; // unique

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $officialCode; // indexed

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $hierarchicalDescription;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getSortingCode(): string
    {
        return $this->sortingCode;
    }

    /**
     * @param string $sortingCode
     */
    public function setSortingCode(string $sortingCode): void
    {
        $this->sortingCode = $sortingCode;
    }

    /**
     * @return string
     */
    public function getOfficialCode(): string
    {
        return $this->officialCode;
    }

    /**
     * @param string $officialCode
     */
    public function setOfficialCode(string $officialCode): void
    {
        $this->officialCode = $officialCode;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getHierarchicalDescription(): string
    {
        return $this->hierarchicalDescription;
    }

    /**
     * @param string $hierarchicalDescription
     */
    public function setHierarchicalDescription(string $hierarchicalDescription): void
    {
        $this->hierarchicalDescription = $hierarchicalDescription;
    }


}