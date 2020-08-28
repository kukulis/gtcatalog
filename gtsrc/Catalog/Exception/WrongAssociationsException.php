<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.5
 * Time: 23.27
 */

namespace Gt\Catalog\Exception;


class WrongAssociationsException extends CatalogDetailedException
{
    /**
     * @var RelatedObject[]
     */
    private $relatedObjects=[];

    /**
     * @return array
     */
    public function getRelatedObjects(): array
    {
        return $this->relatedObjects;
    }

    /**
     * @param array $relatedObjects
     */
    public function setRelatedObjects(array $relatedObjects): void
    {
        $this->relatedObjects = $relatedObjects;
    }

}