<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210605234038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE animal_kind_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE checkup_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE client_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE payment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE service_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE animal_kind (id INT NOT NULL, name VARCHAR(255) NOT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE checkup (id INT NOT NULL, doctor_id INT NOT NULL, pet_id INT NOT NULL, diagnosis VARCHAR(512) NOT NULL, treatment VARCHAR(512) NOT NULL, complaints VARCHAR(512) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FD1B7CAF87F4FB17 ON checkup (doctor_id)');
        $this->addSql('CREATE INDEX IDX_FD1B7CAF966F7FB6 ON checkup (pet_id)');
        $this->addSql('CREATE TABLE checkup_service (checkup_id INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY(checkup_id, service_id))');
        $this->addSql('CREATE INDEX IDX_14C55264BD8A2086 ON checkup_service (checkup_id)');
        $this->addSql('CREATE INDEX IDX_14C55264ED5CA9E6 ON checkup_service (service_id)');
        $this->addSql('CREATE TABLE client (id INT NOT NULL, account_id INT NOT NULL, address VARCHAR(512) NOT NULL, passport VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C74404559B6B5FBA ON client (account_id)');
        $this->addSql('CREATE TABLE payment (id INT NOT NULL, client_id INT NOT NULL, checkup_id INT NOT NULL, registrar_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, sum DOUBLE PRECISION NOT NULL, status VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D28840D19EB6921 ON payment (client_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840DBD8A2086 ON payment (checkup_id)');
        $this->addSql('CREATE INDEX IDX_6D28840DD1AA2FC1 ON payment (registrar_id)');
        $this->addSql('CREATE TABLE pet (id INT NOT NULL, owner_id INT NOT NULL, kind_id INT NOT NULL, name VARCHAR(255) NOT NULL, birthday DATE NOT NULL, sex BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E4529B857E3C61F9 ON pet (owner_id)');
        $this->addSql('CREATE INDEX IDX_E4529B8530602CA9 ON pet (kind_id)');
        $this->addSql('CREATE TABLE service (id INT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, login VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, patronymic VARCHAR(50) DEFAULT NULL, phone VARCHAR(15) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649AA08CB10 ON "user" (login)');
        $this->addSql('ALTER TABLE checkup ADD CONSTRAINT FK_FD1B7CAF87F4FB17 FOREIGN KEY (doctor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkup ADD CONSTRAINT FK_FD1B7CAF966F7FB6 FOREIGN KEY (pet_id) REFERENCES pet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkup_service ADD CONSTRAINT FK_14C55264BD8A2086 FOREIGN KEY (checkup_id) REFERENCES checkup (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkup_service ADD CONSTRAINT FK_14C55264ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404559B6B5FBA FOREIGN KEY (account_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DBD8A2086 FOREIGN KEY (checkup_id) REFERENCES checkup (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DD1AA2FC1 FOREIGN KEY (registrar_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pet ADD CONSTRAINT FK_E4529B857E3C61F9 FOREIGN KEY (owner_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pet ADD CONSTRAINT FK_E4529B8530602CA9 FOREIGN KEY (kind_id) REFERENCES animal_kind (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pet DROP CONSTRAINT FK_E4529B8530602CA9');
        $this->addSql('ALTER TABLE checkup_service DROP CONSTRAINT FK_14C55264BD8A2086');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840DBD8A2086');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D19EB6921');
        $this->addSql('ALTER TABLE pet DROP CONSTRAINT FK_E4529B857E3C61F9');
        $this->addSql('ALTER TABLE checkup DROP CONSTRAINT FK_FD1B7CAF966F7FB6');
        $this->addSql('ALTER TABLE checkup_service DROP CONSTRAINT FK_14C55264ED5CA9E6');
        $this->addSql('ALTER TABLE checkup DROP CONSTRAINT FK_FD1B7CAF87F4FB17');
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C74404559B6B5FBA');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840DD1AA2FC1');
        $this->addSql('DROP SEQUENCE animal_kind_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE checkup_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE client_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE payment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pet_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE service_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE animal_kind');
        $this->addSql('DROP TABLE checkup');
        $this->addSql('DROP TABLE checkup_service');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE pet');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE "user"');
    }
}
