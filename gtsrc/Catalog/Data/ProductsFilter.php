<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.9.2
 * Time: 20.36
 */

namespace Gt\Catalog\Data;


use Gt\Catalog\Entity\Language;

interface ProductsFilter
{
    /**
     * @return string
     */
    public function getLikeSku(): ?string;

    /**
     * @param string $likeSku
     */
    public function setLikeSku(string $likeSku=null): void;

    /**
     * @return string
     */
    public function getLikeName(): ?string;

    /**
     * @param string $likeName
     */
    public function setLikeName(string $likeName=null): void;

    /**
     * @return Language
     */
    public function getLanguage(): ?Language;

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language=null): void;

    public function getLanguageCode();

    public function getLimit();
}