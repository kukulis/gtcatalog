<?php
/**
 * CategoryLanguage.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-12
 * Time: 16:22
 */

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="categories_languages")
 */
class CategoryLanguage
{
    const ALLOWED_FIELDS = [ 'category', 'language', 'name', 'description'];
    /**
     * @var Category
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Category" )
     * @ORM\JoinColumn(name="category", referencedColumnName="code")
     */

    private $category;

    /**
     * @var Language
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Language" )
     * @ORM\JoinColumn(name="language", referencedColumnName="code")
     */
    private $language;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language): void
    {
        $this->language = $language;
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
    public function setName(string $name=null): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description=null): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getCode() {
        return $this->category->getCode();
    }

    /**
     * @return string|null
     */
    public function getLanguageCode() {
        return $this->language->getCode();
    }
}