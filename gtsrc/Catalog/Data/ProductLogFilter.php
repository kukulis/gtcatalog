<?php

namespace Gt\Catalog\Data;

interface ProductLogFilter
{
    /**
     * @return mixed
     */
    public function getOffset();

    /**
     * @return mixed
     */
    public function getLimit();

    /**
     * @return mixed
     */
    public function getLanguage();

    public function getSku();
}