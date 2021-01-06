<?php


namespace Gt\Catalog\Form;

use Gt\Catalog\Entity\ClassificatorGroup;
use Gt\Catalog\Entity\ClassificatorLanguage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ClassificatorFormType extends AbstractType
{
    /** @var ClassificatorLanguage */
    private $classificatorLanguage;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class)
            ->add('group', EntityType::class, [
                'class' => ClassificatorGroup::class,
                'choice_label' => 'name'
            ])
            ->add('value', TextType::class )
            ->add('customscode', TextType::class, ['required'=>false] )
            ->add('save', SubmitType::class, ['label'=>'Saugoti'])
        ;
    }

    /**
     * @return ClassificatorLanguage
     */
    public function getClassificatorLanguage(): ClassificatorLanguage
    {
        return $this->classificatorLanguage;
    }

    /**
     * @param ClassificatorLanguage $classificatorLanguage
     */
    public function setClassificatorLanguage(ClassificatorLanguage $classificatorLanguage): void
    {
        $this->classificatorLanguage = $classificatorLanguage;
    }

    /**
     * @return ClassificatorGroup
     */
    public function getGroup(): ?ClassificatorGroup
    {
        return $this->classificatorLanguage->getClassificator()->getGroup();
    }

    /**
     * @param ClassificatorGroup $group
     */
    public function setGroup(ClassificatorGroup $group): void
    {
        $this->classificatorLanguage->getClassificator()->setGroup($group);
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->classificatorLanguage->getClassificator()->getCode();
    }

    /**
     * @param string $code
     */
    public function setCode(string $code=null): void
    {
        $this->classificatorLanguage->getClassificator()->setCode( $code );
    }

    /**
     * @return null|string
     */
    public function getValue ( ) {
        return $this->classificatorLanguage->getName();
    }

    /**
     * @param $val
     */
    public function setValue ( $val ) {
        $this->classificatorLanguage->setName($val);
    }

    public function getCustomsCode() {
        return $this->classificatorLanguage->getClassificator()->getCustomsCode();
    }

    public function setCustomsCode ( $customsCode ) {
        $this->classificatorLanguage->getClassificator()->setCustomsCode($customsCode);
    }
}