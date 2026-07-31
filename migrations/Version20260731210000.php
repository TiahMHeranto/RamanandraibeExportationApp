<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260731210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create hangar de traitement table (numero, code)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE hangar (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, numero VARCHAR(40) NOT NULL, code VARCHAR(80) NOT NULL, actif BOOLEAN NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_HANGAR_NUMERO ON hangar (numero)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_HANGAR_CODE ON hangar (code)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE hangar');
    }
}
