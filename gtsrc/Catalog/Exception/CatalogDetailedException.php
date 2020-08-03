<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.3
 * Time: 21.59
 */

namespace Gt\Catalog\Exception;


class CatalogDetailedException extends CatalogBaseException
{

    private $details=[];

    /**
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * @param array $details
     */
    public function setDetails(array $details): void
    {
        $this->details = $details;
    }

}