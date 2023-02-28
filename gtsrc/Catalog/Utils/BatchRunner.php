<?php

namespace Gt\Catalog\Utils;

// TODO move to a separate lib

class BatchRunner
{
    public static function runBatch(array $data, int $step, callable $f, ?callable $logger=null): int
    {
        $count = 0;
        for ($i = 0; $i < count($data); $i += $step) {
            if ( $logger != null ) {
                call_user_func($logger, sprintf('Taking from: %s ; record count: %s', $i, $step));
            }

            $part = array_slice($data, $i, $step);
            $count += call_user_func($f, $part);
        }

        return $count;
    }

    public static function runBatchArrayResult(array $data, int $step, callable $f, ?callable $logger=null): array
    {
        $result = [];
        for ($i = 0; $i < count($data); $i += $step) {
            if ( $logger != null ) {
                call_user_func($logger, sprintf('Taking from: %s ; record count: %s', $i, $step));
            }
            $part = array_slice($data, $i, $step);
            $part = call_user_func($f, $part);

            $result = array_merge($result, $part);
        }

        return $result;
    }
}