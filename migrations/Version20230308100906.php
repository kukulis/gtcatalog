<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230308100906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ "CREATE TABLE tmp_pakuotes (
    nomnr char(32),
    -- pragma dalis
    preke_id int,
    pakuotes_tipas int,
    pakuotes_rusis int,
    kiekis_pakuoteje float,
    svoris float,

    -- katalogo dalis
    brandas varchar(64),
    tipas varchar(64),
    kiekis varchar(64),
    primary key ( nomnr, pakuotes_rusis)
)"
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(/** @lang MySQL */ "DROP TABLE tmp_pakuotes");
    }
}
