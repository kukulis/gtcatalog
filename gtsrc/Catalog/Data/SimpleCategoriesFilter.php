<?php
/**
 * SimpleCategoriesFilter.php
 * Created by Giedrius Tumelis.
 * Date: 2021-03-31
 * Time: 10:12
 */

namespace Gt\Catalog\Data;


use Gt\Catalog\Entity\Language;

class SimpleCategoriesFilter implements CategoriesFilter
{
    private $limit;
    private $likeCode;
    private $likeParent;
    private $exactParent;

    /** @return Language */
    private $language;

    private $offset;

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getLikeCode()
    {
        return $this->likeCode;
    }

    /**
     * @param mixed $likeCode
     */
    public function setLikeCode($likeCode): void
    {
        $this->likeCode = $likeCode;
    }

    /**
     * @return mixed
     */
    public function getLikeParent()
    {
        return $this->likeParent;
    }

    /**
     * @param mixed $likeParent
     */
    public function setLikeParent($likeParent): void
    {
        $this->likeParent = $likeParent;
    }

    /**
     * @return mixed
     */
    public function getExactParent()
    {
        return $this->exactParent;
    }

    /**
     * @param mixed $exactParent
     */
    public function setExactParent($exactParent): void
    {
        $this->exactParent = $exactParent;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language): void
    {
        $this->language = $language;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function setOffset($offset): void
    {
        $this->offset = $offset;
    }
}