<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */

final class Version20240808075120_added_barcode_field extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'adding barcode field to product';
    }

    public function up(Schema $schema): void
    {
        if ( $schema->getTable('products')->hasColumn('barcode') ) {
            return;
        }

        $this->addSql(/** @lang MySQL  */ 'ALTER TABLE products ADD barcode BIGINT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL  */ 'ALTER TABLE products DROP barcode');
    }
}
