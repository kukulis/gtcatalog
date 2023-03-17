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
     * @Type("int")
     */
    public $preke_id;
    /**
     * @Type("int")
     */
    public $pakuotes_tipas;
    /**
     * @Type("int")
     */
    public $pakuotes_rusis;
    /**
     * @Type("string")
     */
    public $pakuotes_rusis_name;
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