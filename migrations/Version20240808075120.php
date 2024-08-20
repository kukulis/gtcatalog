<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */

// TODO (s) būtų buvę gerai pervadinti klasę, gale prirašant prasmę. Pvz.: Version20240808075120_add_barcode_to_product
final class Version20240808075120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // TODO (FF) pašalinti viską, kas nesusiję su barkodo stulpeliu.
        $this->addSql('ALTER TABLE products ADD barcode BIGINT DEFAULT NULL, CHANGE weight weight NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, CHANGE length length NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, CHANGE height height NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, CHANGE width width NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, CHANGE weight_bruto weight_bruto NUMERIC(10, 2) DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // TODO (FF) pašalinti viską, kas nesusiję su barkodo stulpeliu.
        $this->addSql('ALTER TABLE products DROP barcode, CHANGE weight weight NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, CHANGE weight_bruto weight_bruto NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, CHANGE length length NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, CHANGE height height NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, CHANGE width width NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL');
    }
}
