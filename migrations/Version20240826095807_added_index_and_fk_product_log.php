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
        $this->addSql('ALTER TABLE product_log ADD CONSTRAINT FK_user_id FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_user_id ON product_log (user)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product_log DROP FOREIGN KEY FK_user_id');
        $this->addSql('DROP INDEX IDX_user_id ON product_log');
    }
}
