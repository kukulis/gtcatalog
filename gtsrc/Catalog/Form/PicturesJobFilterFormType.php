<?php
/**
 * PicturesJobFilterFormType.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-30
 * Time: 15:30
 */

namespace Gt\Catalog\Form;


use Gt\Catalog\Data\IPicturesJobsFilter;
use Symfony\Component\Form\AbstractType;

class PicturesJobFilterFormType extends AbstractType implements IPicturesJobsFilter
{
    private $limit=20;
    private $status;

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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }
}