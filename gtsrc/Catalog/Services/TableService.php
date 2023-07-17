<?php

namespace Gt\Catalog\Services;

use Gt\Catalog\TableData\TableData;
use Twig\Environment;

class TableService
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function generateTableHtml(
        array  $rows,
        array  $columns,
        array  $tableOptions,
        string $languageCode = null
    ): string {
        $tableData = new TableData($rows, $columns, $tableOptions);

        return $this->twig->render('@Catalog/components/table.html.twig', [
            'tableData' => $tableData,
            'languageCode' => $languageCode
        ]);
    }
}