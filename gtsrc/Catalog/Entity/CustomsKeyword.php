<?php
/**
 * CustomsKeyword.php
 * Created by Giedrius Tumelis.
 * Date: 2021-04-07
 * Time: 11:12
 */

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gt\Catalog\Repository\CustomsKeywordsRepository;


/**
 *
 * @ORM\Entity(repositoryClass=CustomsKeywordsRepository::class)
 * @ORM\Table(name="customs_keywords")
 *
 */
class CustomsKeyword
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
     * @ORM\Column(type="string", length=16)
     */
    private $customsCode;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $keyword;

    /**
     * @var string
     * @ORM\Column(type="string", length=66, nullable=true)
     */
    private $likeKeyword;

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
    public function getCustomsCode(): string
    {
        return $this->customsCode;
    }

    /**
     * @param string $customsCode
     */
    public function setCustomsCode(string $customsCode): void
    {
        $this->customsCode = $customsCode;
    }

    /**
     * @return string
     */
    public function getKeyword(): string
    {
        return $this->keyword;
    }

    /**
     * @param string $keyword
     */
    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    /**
     * @return string
     */
    public function getLikeKeyword(): string
    {
        return $this->likeKeyword;
    }

    /**
     * @param string $likeKeyword
     */
    public function setLikeKeyword(string $likeKeyword): void
    {
        $this->likeKeyword = $likeKeyword;
    }
}