<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240723212401_create_product_log_table extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql( /** @lang MySQL */'CREATE TABLE product_log
(
    id           INT AUTO_INCREMENT NOT NULL,
    language     VARCHAR(255) DEFAULT NULL,
    product_old  JSON         DEFAULT NULL,
    product_new  JSON         DEFAULT NULL,
    user         INT                NOT NULL,
    sku          VARCHAR(255)                NOT NULL,
    product_language_new JSON         DEFAULT NULL,
    product_language_old JSON         DEFAULT NULL,
    date_created DATETIME     DEFAULT CURRENT_TIMESTAMP,
    deleted TINYINT(1)  DEFAULT 0 NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4
  COLLATE `utf8mb4_unicode_ci`
  ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(/** @lang MySQL */ 'DROP TABLE product_log');
    }
}
