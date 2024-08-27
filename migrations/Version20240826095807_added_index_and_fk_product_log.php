<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240826095807_added_index_and_fk_product_log extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds index and foreign key to product_log table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL  */'ALTER TABLE product_log ADD CONSTRAINT FK_user_id FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql(/** @lang MySQL  */ 'CREATE INDEX IDX_user_id ON product_log (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL  */ 'ALTER TABLE product_log DROP FOREIGN KEY FK_user_id');
        $this->addSql(/** @lang MySQL  */ 'DROP INDEX IDX_user_id ON product_log');
    }
}
