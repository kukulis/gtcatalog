<?php
/**
 * TmpClassificator.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-23
 * Time: 16:26
 */

namespace Gt\Catalog\Services\Legacy;


class TmpClassificator
{
    public $language_code;
    public $classificator_code;
    public $group_code;
    public $value;

    private $test;

    /**
     * @return mixed
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * @param mixed $test
     */
    public function setTest($test): void
    {
        $this->test = $test;
    }


}