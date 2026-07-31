<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260731201000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create fournisseur table (code, nom, zone)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE fournisseur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(80) NOT NULL, nom VARCHAR(255) NOT NULL, zone VARCHAR(180) NOT NULL, actif BOOLEAN NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FOURNISSEUR_CODE ON fournisseur (code)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE fournisseur');
    }
}
