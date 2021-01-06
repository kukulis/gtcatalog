<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 11.46
 */

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pictures")
 */
class Picture
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
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, unique=true, length=64)
     */
    private $reference;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, length=64)
     */
    private $info_provider;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, length=16)
     */
    private $version;

    /**
     * @var int
     * @ORM\Column(type="integer", length=16, options={"default":1})
     */
    private $sequenceNumber=1;


    // -- not stored to db
    private $configuredPath;

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
     * @return mixed
     */
    public function getConfiguredPath()
    {
        return $this->configuredPath;
    }

    /**
     * @param mixed $configuredPath
     */
    public function setConfiguredPath($configuredPath): void
    {
        $this->configuredPath = $configuredPath;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getInfoProvider(): ?string
    {
        return $this->info_provider;
    }

    /**
     * @param string $info_provider
     */
    public function setInfoProvider(string $info_provider=null): void
    {
        $this->info_provider = $info_provider;
    }

    /**
     * @return string
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version=null): void
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getSequenceNumber(): int
    {
        return $this->sequenceNumber;
    }

    /**
     * @param int $sequenceNumber
     */
    public function setSequenceNumber(int $sequenceNumber): void
    {
        $this->sequenceNumber = $sequenceNumber;
    }
}