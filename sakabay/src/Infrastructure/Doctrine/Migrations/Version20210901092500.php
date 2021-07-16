<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210901092500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE besoin_sous_categories DROP FOREIGN KEY FK_238FC76FE6EED44');
        $this->addSql('ALTER TABLE besoin_sous_categories DROP FOREIGN KEY FK_238FC76A72F5DFC');
        $this->addSql('DROP INDEX IDX_238FC76FE6EED44 ON besoin_sous_categories');
        $this->addSql('ALTER TABLE besoin_sous_categories DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE besoin_sous_categories ADD id_sous_category INT NOT NULL COMMENT \'Identifiant technique d\'\'une sous category\', DROP besoin_id, CHANGE id_besoin id_besoin INT NOT NULL COMMENT \'Identifiant technique d\'\'un besoin\'');
        $this->addSql('ALTER TABLE besoin_sous_categories ADD CONSTRAINT FK_238FC76B02AFCF1 FOREIGN KEY (id_sous_category) REFERENCES sous_category (id)');
        $this->addSql('ALTER TABLE besoin_sous_categories ADD CONSTRAINT FK_238FC76A72F5DFC FOREIGN KEY (id_besoin) REFERENCES besoin (id)');
        $this->addSql('CREATE INDEX IDX_238FC76B02AFCF1 ON besoin_sous_categories (id_sous_category)');
        $this->addSql('ALTER TABLE besoin_sous_categories ADD PRIMARY KEY (id_besoin, id_sous_category)');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F96856F23');
        $this->addSql('DROP INDEX idx_4fbf094f96856f23 ON company');
        $this->addSql('CREATE INDEX IDX_4FBF094FE59D29CC ON company (id_company_statut)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F96856F23 FOREIGN KEY (id_company_statut) REFERENCES company_statut (id)');
        $this->addSql('ALTER TABLE company_sous_categories DROP FOREIGN KEY FK_937A29FF979B1AD6');
        $this->addSql('ALTER TABLE company_sous_categories DROP FOREIGN KEY FK_937A29FF9122A03F');
        $this->addSql('DROP INDEX IDX_937A29FF979B1AD6 ON company_sous_categories');
        $this->addSql('ALTER TABLE company_sous_categories DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE company_sous_categories ADD id_sous_category INT NOT NULL COMMENT \'Identifiant technique d\'\'une sous category\', DROP company_id, CHANGE id_company id_company INT NOT NULL COMMENT \'Identifiant technique d\'\'un company\'');
        $this->addSql('ALTER TABLE company_sous_categories ADD CONSTRAINT FK_937A29FFB02AFCF1 FOREIGN KEY (id_sous_category) REFERENCES sous_category (id)');
        $this->addSql('ALTER TABLE company_sous_categories ADD CONSTRAINT FK_937A29FF9122A03F FOREIGN KEY (id_company) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_937A29FFB02AFCF1 ON company_sous_categories (id_sous_category)');
        $this->addSql('ALTER TABLE company_sous_categories ADD PRIMARY KEY (id_company, id_sous_category)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE besoin_sous_categories DROP FOREIGN KEY FK_238FC76B02AFCF1');
        $this->addSql('ALTER TABLE besoin_sous_categories DROP FOREIGN KEY FK_238FC76A72F5DFC');
        $this->addSql('DROP INDEX IDX_238FC76B02AFCF1 ON besoin_sous_categories');
        $this->addSql('ALTER TABLE besoin_sous_categories DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE besoin_sous_categories ADD besoin_id INT NOT NULL COMMENT \'Identifiant technique d\'\'un besoin\', DROP id_sous_category, CHANGE id_besoin id_besoin INT NOT NULL COMMENT \'Identifiant technique d\'\'une sous category\'');
        $this->addSql('ALTER TABLE besoin_sous_categories ADD CONSTRAINT FK_238FC76FE6EED44 FOREIGN KEY (besoin_id) REFERENCES besoin (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE besoin_sous_categories ADD CONSTRAINT FK_238FC76A72F5DFC FOREIGN KEY (id_besoin) REFERENCES sous_category (id)');
        $this->addSql('CREATE INDEX IDX_238FC76FE6EED44 ON besoin_sous_categories (besoin_id)');
        $this->addSql('ALTER TABLE besoin_sous_categories ADD PRIMARY KEY (besoin_id, id_besoin)');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FE59D29CC');
        $this->addSql('DROP INDEX idx_4fbf094fe59d29cc ON company');
        $this->addSql('CREATE INDEX IDX_4FBF094F96856F23 ON company (id_company_statut)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FE59D29CC FOREIGN KEY (id_company_statut) REFERENCES company_statut (id)');
        $this->addSql('ALTER TABLE company_sous_categories DROP FOREIGN KEY FK_937A29FFB02AFCF1');
        $this->addSql('ALTER TABLE company_sous_categories DROP FOREIGN KEY FK_937A29FF9122A03F');
        $this->addSql('DROP INDEX IDX_937A29FFB02AFCF1 ON company_sous_categories');
        $this->addSql('ALTER TABLE company_sous_categories DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE company_sous_categories ADD company_id INT NOT NULL COMMENT \'Identifiant technique d\'\'un company\', DROP id_sous_category, CHANGE id_company id_company INT NOT NULL COMMENT \'Identifiant technique d\'\'une sous category\'');
        $this->addSql('ALTER TABLE company_sous_categories ADD CONSTRAINT FK_937A29FF979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_sous_categories ADD CONSTRAINT FK_937A29FF9122A03F FOREIGN KEY (id_company) REFERENCES sous_category (id)');
        $this->addSql('CREATE INDEX IDX_937A29FF979B1AD6 ON company_sous_categories (company_id)');
        $this->addSql('ALTER TABLE company_sous_categories ADD PRIMARY KEY (company_id, id_company)');
    }
}
