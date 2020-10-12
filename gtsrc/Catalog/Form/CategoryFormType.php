<?php


namespace Gt\Catalog\Form;


use Gt\Catalog\Entity\Category;
use Gt\Catalog\Entity\CategoryLanguage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFormType extends AbstractType
{
    /** @var CategoryLanguage */
    private $categoryLanguage;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('parent')
            ->add('name')
            ->add('description');
    }

    /**
     * @return CategoryLanguage
     */
    public function getCategoryLanguage(): CategoryLanguage
    {
        return $this->categoryLanguage;
    }

    /**
     * @param CategoryLanguage $categoryLanguage
     */
    public function setCategoryLanguage(CategoryLanguage $categoryLanguage): void
    {
        $this->categoryLanguage = $categoryLanguage;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->categoryLanguage->getCategory()->getCode();
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->categoryLanguage->getCategory()->setCode($code);
    }

    /**
     * @return string
     */
    public function getParentCode(): ?string
    {
        $parent = $this->categoryLanguage->getCategory()->getParent();

        if ( $parent == null ) {
            return null;
        }
        return $parent->getCode();
    }

    /**
     * @param string $parentCode
     */
    public function setParent(string $parentCode=null): void
    {
        if( $parentCode == null ) {
            $this->categoryLanguage->getCategory()->setParent(null);
        }

        $this->categoryLanguage->getCategory()->setParent(Category::createCategory($parentCode));
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->categoryLanguage->getName();
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->categoryLanguage->setName($name);
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->categoryLanguage->getDescription();
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->categoryLanguage->setDescription($description);
    }


}