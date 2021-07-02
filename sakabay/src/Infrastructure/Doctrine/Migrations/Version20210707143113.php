<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210707143113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD title VARCHAR(100) NOT NULL, ADD dt_created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE message message VARCHAR(1000) NOT NULL, CHANGE note note INT NOT NULL');
        $this->addSql('ALTER TABLE company ADD note NUMERIC(3, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP title, DROP dt_created, CHANGE message message VARCHAR(191) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE note note NUMERIC(2, 1) NOT NULL');
        $this->addSql('ALTER TABLE company DROP note');
    }
}
