<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.8
 * Time: 13.37
 */

namespace Gt\Catalog\Data;


interface ClassificatorsListFilter
{
    /**
     * @return mixed
     */
    public function getGroupCode();

    /**
     * @param mixed $groupCode
     */
    public function setGroupCode($groupCode): void;

    /**
     * @return mixed
     */
    public function getLikeCode();

    /**
     * @param mixed $likeCode
     */
    public function setLikeCode($likeCode): void;

    /**
     * @return mixed
     */
    public function getLikeName();

    /**
     * @param mixed $likeName
     */
    public function setLikeName($likeName): void;

    /**
     * @return mixed
     */
    public function getLimit();

    /**
     * @param mixed $limit
     */
    public function setLimit($limit): void;

    public function getLanguageCode(): ?string;
}