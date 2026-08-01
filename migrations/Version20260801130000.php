<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260801130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Stock & traitement: article, couleur, magasin, contrat, mouvement, traitement, pointage + alter arrivage/hangar/personnel';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE couleur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(40) NOT NULL, libelle VARCHAR(120) NOT NULL, actif BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_COULEUR_CODE ON couleur (code)');

        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(40) NOT NULL, libelle VARCHAR(180) NOT NULL, famille VARCHAR(255) NOT NULL, actif BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ARTICLE_CODE ON article (code)');

        $this->addSql('CREATE TABLE magasin (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(40) NOT NULL, nom VARCHAR(120) NOT NULL, actif BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_MAGASIN_CODE ON magasin (code)');

        $this->addSql('CREATE TABLE contrat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reference VARCHAR(60) NOT NULL, libelle VARCHAR(255) DEFAULT NULL, actif BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CONTRAT_REFERENCE ON contrat (reference)');

        $this->addSql('CREATE TABLE traitement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, hangar_id INTEGER NOT NULL, trieuse_id INTEGER NOT NULL, controleuse_id INTEGER DEFAULT NULL, fournisseur_id INTEGER NOT NULL, contrat_id INTEGER DEFAULT NULL, article_source_id INTEGER NOT NULL, couleur_source_id INTEGER NOT NULL, magasin_id INTEGER NOT NULL, reference VARCHAR(40) NOT NULL, date_traitement DATE NOT NULL, poids_sortie NUMERIC(12, 3) NOT NULL, observations CLOB DEFAULT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_TRT_HANGAR FOREIGN KEY (hangar_id) REFERENCES hangar (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_TRT_TRIEUSE FOREIGN KEY (trieuse_id) REFERENCES personnel (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_TRT_CTRL FOREIGN KEY (controleuse_id) REFERENCES personnel (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_TRT_FRNS FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_TRT_CONTRAT FOREIGN KEY (contrat_id) REFERENCES contrat (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_TRT_ART FOREIGN KEY (article_source_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_TRT_COUL FOREIGN KEY (couleur_source_id) REFERENCES couleur (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_TRT_MAG FOREIGN KEY (magasin_id) REFERENCES magasin (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_TRT_HANGAR ON traitement (hangar_id)');
        $this->addSql('CREATE INDEX IDX_TRT_TRIEUSE ON traitement (trieuse_id)');
        $this->addSql('CREATE INDEX IDX_TRT_DATE ON traitement (date_traitement)');

        $this->addSql('CREATE TABLE traitement_ligne (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, traitement_id INTEGER NOT NULL, article_id INTEGER NOT NULL, couleur_id INTEGER NOT NULL, categorie VARCHAR(255) NOT NULL, poids NUMERIC(12, 3) NOT NULL, nombre INTEGER DEFAULT NULL, CONSTRAINT FK_TL_TRT FOREIGN KEY (traitement_id) REFERENCES traitement (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_TL_ART FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_TL_COUL FOREIGN KEY (couleur_id) REFERENCES couleur (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_TL_TRT ON traitement_ligne (traitement_id)');

        $this->addSql('CREATE TABLE mouvement_stock (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, article_id INTEGER NOT NULL, couleur_id INTEGER NOT NULL, magasin_id INTEGER NOT NULL, fournisseur_id INTEGER DEFAULT NULL, contrat_id INTEGER DEFAULT NULL, hangar_id INTEGER DEFAULT NULL, arrivage_id INTEGER DEFAULT NULL, traitement_id INTEGER DEFAULT NULL, date_mouvement DATE NOT NULL, sens VARCHAR(255) NOT NULL, type_operation VARCHAR(255) NOT NULL, poids NUMERIC(12, 3) NOT NULL, reference VARCHAR(60) DEFAULT NULL, observations CLOB DEFAULT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_MS_ART FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_MS_COUL FOREIGN KEY (couleur_id) REFERENCES couleur (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_MS_MAG FOREIGN KEY (magasin_id) REFERENCES magasin (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_MS_FRNS FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_MS_CONTRAT FOREIGN KEY (contrat_id) REFERENCES contrat (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_MS_HANGAR FOREIGN KEY (hangar_id) REFERENCES hangar (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_MS_ARR FOREIGN KEY (arrivage_id) REFERENCES arrivage (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_MS_TRT FOREIGN KEY (traitement_id) REFERENCES traitement (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_MOUVEMENT_DATE ON mouvement_stock (date_mouvement)');

        $this->addSql('CREATE TABLE pointage_jour (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, personnel_id INTEGER NOT NULL, date_pointage DATE NOT NULL, present BOOLEAN NOT NULL, observations CLOB DEFAULT NULL, CONSTRAINT FK_PJ_PERS FOREIGN KEY (personnel_id) REFERENCES personnel (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_POINTAGE_DATE_PERSONNEL ON pointage_jour (date_pointage, personnel_id)');

        // Alter existing tables via recreate pattern for SQLite
        $this->addSql('ALTER TABLE hangar ADD COLUMN magasin_id INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE personnel ADD COLUMN role VARCHAR(255) DEFAULT \'les_deux\' NOT NULL');
        $this->addSql('ALTER TABLE arrivage ADD COLUMN article_id INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE arrivage ADD COLUMN couleur_id INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE arrivage ADD COLUMN magasin_id INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE arrivage ADD COLUMN contrat_id INTEGER DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE mouvement_stock');
        $this->addSql('DROP TABLE traitement_ligne');
        $this->addSql('DROP TABLE traitement');
        $this->addSql('DROP TABLE pointage_jour');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE couleur');
        $this->addSql('DROP TABLE magasin');
        $this->addSql('DROP TABLE contrat');
    }
}
