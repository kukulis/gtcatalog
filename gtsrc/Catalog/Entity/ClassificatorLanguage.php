<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.7.11
 * Time: 18.59
 */

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="classificator_lang")
 */
class ClassificatorLanguage
{
    /**
     * @var Language
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Language" )
     * @ORM\JoinColumn(name="language_code", referencedColumnName="code")
     */
    private $language;

    /**
     * @var Classificator
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Classificator" )
     * @ORM\JoinColumn(name="classificator_code", referencedColumnName="code")
     */
    private $classificator;


    /**
     * @var string
     * @ORM\Column(type="string", name="value")
     */
    private $value;

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
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
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
        $classificatorLang->setValue($name);
        $classificatorLang->setClassificator($classificator);
        return $classificatorLang;
    }
}