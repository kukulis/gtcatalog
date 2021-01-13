<?php
/**
 * FileHelper.php
 * Created by Giedrius Tumelis.
 * Date: 2021-01-13
 * Time: 16:24
 */

namespace Gt\Catalog\Utils;


class FileHelper
{
    public static function getFiles($dir) {
        $filesList = [$dir];
        $i = 0;
        while ( $i < count ($filesList)) {
            $currentFile = $filesList[$i];
            if ( is_dir($currentFile)) {
                $appends = scandir($currentFile);

                foreach ($appends as $appendFile ) {
                    if ( str_starts_with( $appendFile, '.' )) {
                        continue;
                    }
                    $appendPath = $currentFile.DIRECTORY_SEPARATOR.$appendFile;
                    $filesList[] = $appendPath;
                }
            }
            $i++;
        }

        return $filesList;
    }
}