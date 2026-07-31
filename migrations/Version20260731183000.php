<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * SQLite-compatible schema for local development.
 */
final class Version20260731183000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create exportation app schema: user, client, product, shipment, shipment_line';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, company_name VARCHAR(180) NOT NULL, contact_name VARCHAR(120) NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(40) DEFAULT NULL, country VARCHAR(100) NOT NULL, city VARCHAR(120) DEFAULT NULL, address CLOB DEFAULT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(160) NOT NULL, sku VARCHAR(60) NOT NULL, category VARCHAR(80) NOT NULL, unit VARCHAR(20) NOT NULL, unit_price NUMERIC(12, 2) NOT NULL, description CLOB DEFAULT NULL, active BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE shipment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, reference VARCHAR(40) NOT NULL, status VARCHAR(255) NOT NULL, origin_port VARCHAR(120) NOT NULL, destination_port VARCHAR(120) NOT NULL, departure_date DATE DEFAULT NULL, arrival_date DATE DEFAULT NULL, notes CLOB DEFAULT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_SHIPMENT_CLIENT FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2CB20DC19EB6921 ON shipment (client_id)');
        $this->addSql('CREATE TABLE shipment_line (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, shipment_id INTEGER NOT NULL, product_id INTEGER NOT NULL, quantity NUMERIC(12, 3) NOT NULL, unit_price NUMERIC(12, 2) NOT NULL, CONSTRAINT FK_LINE_SHIPMENT FOREIGN KEY (shipment_id) REFERENCES shipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_LINE_PRODUCT FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5F73D7FB7BE036FC ON shipment_line (shipment_id)');
        $this->addSql('CREATE INDEX IDX_5F73D7FB4584665A ON shipment_line (product_id)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(120) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE shipment_line');
        $this->addSql('DROP TABLE shipment');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE "user"');
    }
}
