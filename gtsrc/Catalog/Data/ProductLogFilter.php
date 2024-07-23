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
    public function getLikeName();

    public function getStartsLike();
}