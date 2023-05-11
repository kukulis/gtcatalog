<?php

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
    public function setLikeSku(string $likeSku = null): void;

    /**
     * @return string
     */
    public function getLikeName(): ?string;

    /**
     * @param string $likeName
     */
    public function setLikeName(string $likeName = null): void;

    /**
     * @return Language
     */
    public function getLanguage(): ?Language;

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language = null): void;

    public function getLanguageCode();

    public function getLimit();

    public function getCategory(): ?string;

    public function getDateFrom(): ?string;

    public function getDateTill(): ?string;

    public function getBrand(): ?string;
}