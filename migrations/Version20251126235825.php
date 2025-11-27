<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251126235825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider ADD endpoint_url VARCHAR(255) NOT NULL, ADD is_active TINYINT(1) NOT NULL, DROP status, DROP price, DROP message');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider ADD status VARCHAR(20) DEFAULT NULL, ADD price DOUBLE PRECISION DEFAULT NULL, ADD message VARCHAR(255) DEFAULT NULL, DROP endpoint_url, DROP is_active');
    }
}
