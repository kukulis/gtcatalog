<?php

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="classificator_lang")
 */
class ClassificatorLanguage
{
    const ALLOWED_FIELDS = [
        'classificator',
        'language',
        'name',
    ];

    /**
     * @var Language
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Language" )
     * @ORM\JoinColumn(name="language", referencedColumnName="code")
     */
    private $language;

    /**
     * @var Classificator
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Classificator" )
     * @ORM\JoinColumn(name="classificator", referencedColumnName="code")
     */
    private $classificator;


    /**
     * @var string
     * @ORM\Column(type="string", name="name")
     */
    private $name;

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
     * @return Classificator
     */
    public function getClassificator(): Classificator
    {
        return $this->classificator;
    }

    /**
     * @param Classificator $classificator
     */
    public function setClassificator(Classificator $classificator): void
    {
        $this->classificator = $classificator;
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
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $code
     * @param string $groupCode
     * @return ClassificatorLanguage
     */
    public static function createLangClassificator ( $code, $name, $groupCode, Language $language ) {
        $classificator = Classificator::createClassificator($code, $groupCode);
        $classificatorLang = new ClassificatorLanguage();
        $classificatorLang->setLanguage($language);
        $classificatorLang->setName($name);
        $classificatorLang->setClassificator($classificator);
        return $classificatorLang;
    }
}