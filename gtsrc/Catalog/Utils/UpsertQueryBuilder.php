<?php

namespace Gt\Catalog\Utils;

// TODO move to a separate lib

use Closure;

class UpsertQueryBuilder
{
    public function buildUpsertQueryFromDataArraysForMysql(
        Closure $quoter,
        array $dataArrays,
        string $tableName,
        array $columnNames,
        array $updatedColumnNames
    ) {
        $collectedValuesLines = [];

        foreach ($dataArrays as $dataArray) {
            $values = self::getValuesByGivenOrderedKeys($dataArray, $columnNames);
            $quotedValues = array_map($quoter, $values);
            $collectedValuesLines[] = sprintf('(%s)', join(',', $quotedValues));
        }

        $valuesString = join(",\n", $collectedValuesLines);
        // back quotes might be needed
        $columnNamesStr = join(',', $columnNames);


        $updateColumnLines = [];
        foreach ($updatedColumnNames as $columnName) {
            $updateColumnLines[] = sprintf('%s = values(%s)', $columnName, $columnName);
        }

        $updates = join(",\n", $updateColumnLines);

        $updatePart = '';
        if (count($updateColumnLines) > 0) {
            $updatePart = sprintf(
                'ON duplicate key update
                        %s',
                $updates
            );
        }

        return sprintf(
            "INSERT INTO %s (%s)
                        VALUES %s
                        %s",
            $tableName,
            $columnNamesStr,
            $valuesString,
            $updatePart
        );
    }

    public static function getValuesByGivenOrderedKeys(array $valuesMap, array $keys)
    {
        $values = [];
        foreach ($keys as $key) {
            $values[] = $valuesMap[$key] ?? null;
        }

        return $values;
    }

}
