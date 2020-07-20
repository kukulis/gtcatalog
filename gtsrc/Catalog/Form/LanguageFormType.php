<?php


namespace Gt\Catalog\Form;


use Gt\Catalog\Entity\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageFormType extends AbstractType
{
    /** @var Language */
    private $language;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('localeCode');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Language::class
        ]);
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
     * Delegating
     * @return string
     */
    public function getCode()
    {
        return $this->language->getCode();
    }

    /**
     * Delegating
     * @param string $code
     */
    public function setCode($code): void
    {
        $this->language->setCode( $code);
    }

    /**
     * Delegating
     * @return string
     */
    public function getName()
    {
        return $this->language->getName();
    }

    /**
     * Delegating
     * @param string $name
     */
    public function setName($name): void
    {
        $this->language->setName( $name );
    }

    /**
     * @return string
     */
    public function getLocaleCode(): string
    {
        return $this->language->getLocaleCode();
    }

    /**
     * @param string $localeCode
     */
    public function setLocaleCode(string $localeCode): void
    {
        $this->language->setLocaleCode( $localeCode );
    }


}