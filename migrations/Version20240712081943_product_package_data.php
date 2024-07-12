<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Gt\Catalog\Entity\PackageType;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240712081943_product_package_data extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Testing data for products packages';
    }

    public function up(Schema $schema): void
    {
        // These will be the real packages types:
        $this->addSql(
        /** @lang MySQL */ "INSERT IGNORE into packages_types (code, description )
            values
               ('plastic', 'Plastikas'),
               ('metal', 'Metalas'),
               ('glass', 'Stiklas'),
               ('paper', 'Popierius')"
        );

        $this->addSql(
        /** @lang MySQL */ "INSERT into products_packages (sku, type_code, weight )
            values 
                ('0000-01660', 'plastic', '0.5'),
                ('0000-01660', 'paper', '0.4'),
                ('0000-02063', 'glass', '0.6'),
                ('0000-02063', 'metal', '0.1')"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
        /** @lang MySQL */ "DELETE from products_packages
            WHERE sku in ('0000-01660', '0000-02063')"
        );
    }
}
