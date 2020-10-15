<?php
/**
 * RestPrekeAtributai.php
 * Created by Giedrius Tumelis.
 * Date: 18.7.30
 * Time: 11.04
 */

namespace Gt\Catalog\Rest\Legacy;

class Atributai
{
    public $nomnr;

    /**
     * Prekės spalvos RGB kodas, nenaudoti stringinėms reprezentacijoms
     * @var string
     */
    public $spalva;

    /**
     * @var string
     */
    public $garantija;

    /**
     * Tiekėjo, tiekiančio šią prekę, identifikatorius
     * @var string
     * @deprecated netenkinamas kardinalumas, prekę su tuo pačiu barkodu gali tiekti keli tiekėjai
     */
    public $tiekejo_kodas;

    /**
     * gamintojo suteiktas unikalus prekės numeris - mpn
     * @var string
     */
    public $gamintojo_kodas;

    public $svoris;
    public $ilgis;
    public $aukstis;
    public $plotis;

    /**
     * @depredated nenaudoti, pristatymo laikas priklauso nuo tiekėjo/ų ir gaunamas iš StockData kartu su likučiais
     */
    public $pristatymo_laikas;

    public $tipas;
    public $tipas_title;

    /**
     * paskirties identifikatorius
     * @deprecated
     * @var string
     */
    public $paskirtis;

    /**
     * paskirties pavadinimas
     * @var string
     */
    public $paskirtis_title;

    public $vyrams;
    public $moterims;

    /**
     * Dydis yra intervalas reikšmių, į kurį turi pakliūti $kiekis, naudojamas filtravimui.
     * @var string
     */
    public $dydis;

    /**
     * Konkreti reikšmė
     * @var string
     */
    public $kiekis;

    /**
     * Prekių mato vieneto identifikatorius: vnt, g, ml. Juos reikia imti iš klasifikatorių
     * @var string
     */
    public $matas;

    /**
     * Prekių mato vienetai: vnt, g, ml. Jį reikia imti iš klasifikatorių pagal $matas
     * @var string
     */
    public $matas_title;

    /**
     * Čia kategorijų identifikatoriai
     *
     * @var string[]
     */
    public $kategorijos;

    /**
     * Čia kategorijų pavadinimai
     *
     * @var string[]
     * @deprecated vietoj jo naudoti KatalogasPreke->categories
     */
    public $kategorijos_titles;

    public $tagai;

    public $pack_size;

    /**
     * prekių grupės identifikatorius, nepainioti su pragmos prekių grupėmis, čia kitos, ne tas pats
     * @var string
     */
    public $prekiu_grupe;

    /**
     * prekių grupės pavadinimas, nepainioti su pragmos prekių grupėmis, čia kitos, ne tas pats
     * @var string
     */
    public $prekiu_grupe_title;

    public $priority;

    /**
     * Čia Kategorijos identifikatorius
     * @var string
     */
    public $google_product_category;

    /**
     * Čia Kategorijos pavadinimas pasirinkta kalba
     * @var string
     */
    public $google_product_category_title;

    public $info_provider;
}