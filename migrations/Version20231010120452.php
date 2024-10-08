<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231010120452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql( /** @lang MySQL */'ALTER TABLE products_languages CHANGE name name varchar(255) DEFAULT \'-\' not null');
    }

    public function down(Schema $schema): void
    {
    }
}
