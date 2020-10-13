<?php


namespace Gt\Catalog\Form;


use Gt\Catalog\Entity\Category;
use Gt\Catalog\Entity\CategoryLanguage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryFormType extends AbstractType
{
    /** @var CategoryLanguage */
    private $categoryLanguage;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, ['disabled' => true] )
            ->add('parentCode', TextType::class, ['required' => false ] )
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('save', SubmitType::class, ['label'=>'Saugoti']);
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
     * @param null $parentCode
     */
    public function setParentCode ( $parentCode = null ) {
        if ( $parentCode == null ) {
            $this->categoryLanguage->getCategory()->setParent(null);
            return;
        }
        $this->categoryLanguage->getCategory()->setParent( Category::createCategory($parentCode) );
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
    public function getName(): ?string
    {
        return $this->categoryLanguage->getName();
    }

    /**
     * @param string $name
     */
    public function setName(string $name=null): void
    {
        $this->categoryLanguage->setName($name);
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->categoryLanguage->getDescription();
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description=null): void
    {
        $this->categoryLanguage->setDescription($description);
    }
}