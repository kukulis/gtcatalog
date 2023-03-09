<?php

namespace Gt\Catalog\Data;

use JMS\Serializer\Annotation\Type;
class TmpPackage
{
    /**
     * @Type("string")
     */
    public $nomnr;

//  pragma dalis
    /**
     * @Type("string")
     */
    public $preke_id;
    /**
     * @Type("string")
     */
    public $pakuotes_tipas;
    /**
     * @Type("string")
     */
    public $pakuotes_rusis;
    /**
     * @Type("string")
     */
    public $kiekis_pakuoteje;
    /**
     * @Type("string")
     */
    public $svoris;

    // katalogo dalis
    /**
     * @Type("string")
     */
    public $brandas;
    /**
     * @Type("string")
     */
    public $tipas;
    /**
     * @Type("string")
     */
    public $kiekis;

    /**
     * @Type("string")
     */
    public $svoris_bruto;
}