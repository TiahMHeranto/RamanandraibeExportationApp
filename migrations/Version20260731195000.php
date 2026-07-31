<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260731195000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create personnel table for staff management';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE personnel (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, numero_personnel VARCHAR(30) NOT NULL, nom VARCHAR(255) NOT NULL, actif BOOLEAN NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PERSONNEL_NUMERO ON personnel (numero_personnel)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE personnel');
    }
}
