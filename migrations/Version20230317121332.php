<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230317121332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql(
        /** @lang MySQL */ "ALTER TABLE tmp_pakuotes add column pakuotes_rusis_name varchar(64)"
        );

    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ "ALTER TABLE tmp_pakuotes drop column pakuotes_rusis_name"
        );

    }
}
