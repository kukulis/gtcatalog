<?php
/**
 * RecursiveFilesScanner.php
 * Created by Giedrius Tumelis.
 * Date: 2021-02-15
 * Time: 13:26
 */

namespace Gt\Catalog\Utils;


class RecursiveFilesScanner
{
    private $initialDirectory;

    /**
     * RecursiveFilesScanner constructor.
     * @param $initialDirectory
     */
    public function __construct($initialDirectory)
    {
        $this->initialDirectory = $initialDirectory;
    }

    /**
     * @param IFilesScanListener $listener
     */
    public function scan(IFilesScanListener $listener) {
        $this->scanInner('', $listener);
    }


    public function scanInner($currentDir, IFilesScanListener $listener) {
        $realDir = $this->initialDirectory.DIRECTORY_SEPARATOR.$currentDir;
        $elements = scandir($realDir);
        foreach ($elements as $element) {
            // skip those who start with '.'
            if ( str_starts_with($element, '.')) {
                continue;
            }
            $localPath = $currentDir . DIRECTORY_SEPARATOR. $element;

            $realPath  = $this->initialDirectory.DIRECTORY_SEPARATOR.$localPath;
            if ( is_dir ($realPath) ) {
                $this->scanInner($localPath, $listener);
            }
            else if (is_file($realPath)) {
                $listener->fileEncountered($localPath);
            }
        }
    }
}