<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240819103717_add_column_update_priority extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding update_priority columns to products';
    }

    public function up(Schema $schema): void
    {
        $this->addSql( /** @lang MySQL */'ALTER TABLE products ADD update_priority INT DEFAULT 2 NOT NULL, CHANGE weight weight NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, CHANGE length length NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, CHANGE height height NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, CHANGE width width NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, CHANGE weight_bruto weight_bruto NUMERIC(10, 2) DEFAULT \'0\' NOT NULL');

        // pagal logiką sekančių nebereikia
//        $this->addSql( /** @lang MySQL */'ALTER TABLE products_languages ADD update_priority INT DEFAULT 2 NOT NULL');
//        $this->addSql( /** @lang MySQL */'ALTER TABLE products_packages ADD update_priority INT DEFAULT 2 NOT NULL, CHANGE weight weight NUMERIC(10, 2) DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql( /** @lang MySQL */'ALTER TABLE products DROP update_priority, CHANGE weight weight NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, CHANGE weight_bruto weight_bruto NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, CHANGE length length NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, CHANGE height height NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, CHANGE width width NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL');

        // pagal logiką sekančių nebereikia
//        $this->addSql( /** @lang MySQL */'ALTER TABLE products_languages DROP update_priority');
//        $this->addSql( /** @lang MySQL */'ALTER TABLE products_packages DROP update_priority, CHANGE weight weight NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL');
    }
}
