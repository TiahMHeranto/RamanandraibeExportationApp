<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260801120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create arrivage table (numero, fournisseur, origine, poids, date)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE arrivage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fournisseur_id INTEGER NOT NULL, numero VARCHAR(40) NOT NULL, origine VARCHAR(180) NOT NULL, poids NUMERIC(12, 3) NOT NULL, date_arrivage DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, CONSTRAINT FK_ARRIVAGE_FOURNISSEUR FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ARRIVAGE_NUMERO ON arrivage (numero)');
        $this->addSql('CREATE INDEX IDX_ARRIVAGE_FOURNISSEUR ON arrivage (fournisseur_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE arrivage');
    }
}
