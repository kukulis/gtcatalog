<?php
/**
 * TmpDao.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-23
 * Time: 14:15
 */

namespace Gt\Catalog\Services\Legacy;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Dao\BaseDao;
use Psr\Log\LoggerInterface;
use \PDO;


/**
 * This is may be not a legacy class, because we might reuse it in the future imports.
 * Class TmpDao
 * @package Gt\Catalog\Services\Legacy
 */
class TmpDao
{
    const TMP_PRODUCTS_FIELDS =[
        'sku',
        'last_update',
        'version',
        'parent_sku',
        'origin_country_code',
        'color',
        'for_male',
        'for_female',
        'size',
        'pack_size',
        'pack_amount',
        'weight',
        'length',
        'height',
        'width',
        'delivery_time',
        'info_provider',
        'brand',
        'line',
        'vendor',
        'manufacturer',
        'type',
        'purpose',
        'measure',
        'productgroup',
        'deposit_code',
        'code_from_custom',
        'guaranty',
        'code_from_supplier',
        'code_from_vendor',
        'priority',
        'google_product_category_id',
    ];

    const TMP_PRODUCTS_LANGUAGES_FIELDS = [
        'sku',
        'language',
        'name',
        'short_description',
        'description',
        'label',
        'variant_name',
        'info_provider',
        'tags',
        'label_size',
        'distributor',
        'composition',
    ];

    const TMP_CLASSIFICATORS_FIELDS = [
        'language_code',
        'classificator_code',
        'group_code',
        'value',
    ];
    
    const TMP_CATEGORIES_FIELDS = [
        'category',
        'parent',
        'sku',
        'language',
        'name',
        'description',
    ];

    const TMP_PICTURES_FIELDS = [
         'priority',
         'sku',
         'picture_id',
         'legacy_id',
         'url',
         'name',
         'info_provider',
         'statusas',
    ];

    /** @var LoggerInterface */
    private $logger;

    /** @var Registry */
    private $doctrine;

    /**
     * TmpDao constructor.
     * @param LoggerInterface $logger
     * @param Registry $doctrine
     */
    public function __construct(LoggerInterface $logger, Registry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    /**
     * @return string[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllSkus() {
        /** @var EntityManager $em */
        $em  = $this->doctrine->getManager();
        $conn = $em->getConnection();
        $sql = /** @lang MySQL */ 'select sku from tmp_skus1';
        /** @var string[] $rez */
        $rez = $conn->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        return $rez;
    }

    /**
     * @param TmpFullProduct[] $tmpProducts
     * @return int
     * @throws DBALException
     */
    public function importProducts($tmpProducts) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();
        $quoter = BaseDao::getQuoter($conn);

        $updateFields = array_diff( self::TMP_PRODUCTS_FIELDS, ['sku'] );
        $sql = BaseDao::buildImportSql($tmpProducts, self::TMP_PRODUCTS_FIELDS, $updateFields, $quoter,
            null, 'tmp_full_products');

        return $conn->exec($sql);
    }

    /**
     * @param TmpFullProductLanguage[] $tmpProductsLanguages
     * @return int
     * @throws DBALException
     */
    public function importProductsLanguages($tmpProductsLanguages) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();
        $quoter = BaseDao::getQuoter($conn);

        $updateFields = array_diff( self::TMP_PRODUCTS_LANGUAGES_FIELDS, ['sku', 'language'] );
        $sql = BaseDao::buildImportSql($tmpProductsLanguages, self::TMP_PRODUCTS_LANGUAGES_FIELDS, $updateFields, $quoter,
            null, 'tmp_full_products_languages');

        return $conn->exec($sql);
    }

    public function importClassificators ($tmpClassificators) {
        if ( count($tmpClassificators) == 0  ) {
            return 0;
        }
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();
        $quoter = BaseDao::getQuoter($conn);

        $updateFields = array_diff( self::TMP_CLASSIFICATORS_FIELDS, ['classificator_code'] );
        $sql = BaseDao::buildImportSql($tmpClassificators, self::TMP_CLASSIFICATORS_FIELDS, $updateFields, $quoter,
            null, 'tmp_classificators');

        return $conn->exec($sql);
    }

    public function importCategories ( $tmpCategories ) {
        if ( count($tmpCategories) == 0 ) {
            return 0;
        }
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();
        $quoter = BaseDao::getQuoter($conn);

        $updateFields = array_diff( self::TMP_CATEGORIES_FIELDS, ['category', 'sku'] );
        $sql = BaseDao::buildImportSql($tmpCategories, self::TMP_CATEGORIES_FIELDS, $updateFields, $quoter,
            null, 'tmp_products_categories');

        return $conn->exec($sql);
    }



    public function  importPictures ($tmpProductsPictures) {
        if( count($tmpProductsPictures) == 0 ) {
            return 0;
        }

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();
        $quoter = BaseDao::getQuoter($conn);

        $updateFields = array_diff( self::TMP_PICTURES_FIELDS, ['legacy_id'] );
        $sql = BaseDao::buildImportSql($tmpProductsPictures, self::TMP_PICTURES_FIELDS, $updateFields, $quoter,
            null, 'tmp_products_pictures');

        return $conn->exec($sql);
    }

    /**
     * @return TmpProductPicture[]
     * @throws DBALException
     */
    public function getAllUnuploadedTmpPictures() {
        $sql = /** @lang MySQL */ "SELECT * from tmp_products_pictures
        where (is_downloaded is null or is_downloaded != 1) and picture_id is not null";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();

        /** @var TmpProductPicture[] $pics */
        $pics = $conn->executeQuery($sql)->fetchAll(PDO::FETCH_CLASS, TmpProductPicture::class);
        return $pics;
    }

    /**
     * @param string[] $references
     * @return int
     * @throws DBALException
     */
    public function updateDownloaded ( $references ) {
        if ( count($references) == 0 ) {
            return 0;
        }
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();
        $qReferences = array_map ([$conn, 'quote'], $references);
        $referencesStr = join ( ',', $qReferences );
        $sql = /** @lang MySQL */  "update tmp_products_pictures set is_downloaded=1 where  legacy_id in ($referencesStr)";
        return $conn->exec($sql);
    }
}