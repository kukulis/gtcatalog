<?php
/**
 * ClosureFilesScanListener.php
 * Created by Giedrius Tumelis.
 * Date: 2021-02-15
 * Time: 13:48
 */

namespace Gt\Catalog\Utils;


class ClosureFilesScanListener implements IFilesScanListener {

    /** @var \Closure */
    private $closure;

    /**
     * FilesScanListener constructor.
     * @param \Closure $closure
     */
    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }


    public function fileEncountered($filePath)
    {
        call_user_func($this->closure, $filePath);
    }
}
