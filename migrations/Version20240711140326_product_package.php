<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240711140326_product_package extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // --- packages_types ---
        $this->addSql(
        /** @lang MySQL */ "CREATE TABLE packages_types
            (
                code        VARCHAR(32)  NOT NULL,
                description VARCHAR(255) NOT NULL,
                PRIMARY KEY (code)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        "
        );


        // --- products_packages ---
        $this->addSql(
        /** @lang MySQL */ "CREATE TABLE products_packages
            (
            id        INT AUTO_INCREMENT NOT NULL,
            sku       VARCHAR(32)    DEFAULT NULL,
            type_code VARCHAR(32)    DEFAULT NULL,
            weight    NUMERIC(10, 2) DEFAULT '0' NOT NULL,
            INDEX     IDX_products_packages_sku (sku),
            INDEX     IDX_products_packages_type (type_code),
            UNIQUE INDEX uk_products_packages (sku, type_code),
            PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );

        // --- foreign key to products ---
        $this->addSql(
        /** @lang MySQL */ "ALTER TABLE products_packages
            ADD CONSTRAINT FK_products_packages_product FOREIGN KEY (sku) REFERENCES products (sku)"
        );

        // --- foreign key to types ---
        $this->addSql(
        /** @lang MySQL */ "ALTER TABLE products_packages
            ADD CONSTRAINT FK_products_packages_type FOREIGN KEY (type_code) REFERENCES packages_types (code)"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */ 'ALTER TABLE products_packages DROP FOREIGN KEY FK_products_packages_product');
        $this->addSql(/** @lang MySQL */'ALTER TABLE products_packages DROP FOREIGN KEY FK_products_packages_type');
        $this->addSql(/** @lang MySQL */'DROP TABLE packages_types');
        $this->addSql(/** @lang MySQL */'DROP TABLE products_packages');
    }
}
