<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210511103922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un address\', postal_address VARCHAR(191) NOT NULL, postal_code INT NOT NULL, latitude NUMERIC(9, 6) NOT NULL, longitude NUMERIC(9, 6) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant une adresse.\' ');
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'une answer\', company_id INT DEFAULT NULL COMMENT \'Identifiant technique d\'\'un company\', besoin_id INT DEFAULT NULL COMMENT \'Identifiant technique d\'\'un besoin\', message VARCHAR(1500) NOT NULL, dt_created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, request_quote TINYINT(1) DEFAULT NULL, quote VARCHAR(100) DEFAULT NULL, message_email VARCHAR(1500) DEFAULT NULL, INDEX IDX_DADD4A25979B1AD6 (company_id), INDEX IDX_DADD4A25FE6EED44 (besoin_id), UNIQUE INDEX besoin_company_index (besoin_id, company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant une Anwser.\' ');
        $this->addSql('CREATE TABLE besoin (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un besoin\', id_category INT NOT NULL COMMENT \'Identifiant technique d\'\'un category\', id_author INT NOT NULL COMMENT \'Identifiant technique d\'\'un user\', id_besoin_statut INT NOT NULL COMMENT \'Identifiant technique d\'\'un Besoin Statut\', id_company INT DEFAULT NULL COMMENT \'Identifiant technique d\'\'un company\', id_company_selected INT DEFAULT NULL COMMENT \'Identifiant technique d\'\'un company\', title VARCHAR(191) NOT NULL, description VARCHAR(1000) NOT NULL, dt_created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, dt_updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_8118E8115697F554 (id_category), INDEX IDX_8118E8119B986D25 (id_author), INDEX IDX_8118E8119CB4C5D1 (id_besoin_statut), INDEX IDX_8118E8119122A03F (id_company), INDEX IDX_8118E81163B4FF3A (id_company_selected), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant un Besoin.\' ');
        $this->addSql('CREATE TABLE besoin_sous_categories (besoin_id INT NOT NULL COMMENT \'Identifiant technique d\'\'un besoin\', id_besoin INT NOT NULL COMMENT \'Identifiant technique d\'\'une sous category\', INDEX IDX_238FC76FE6EED44 (besoin_id), INDEX IDX_238FC76A72F5DFC (id_besoin), PRIMARY KEY(besoin_id, id_besoin)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE besoin_statut (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un Besoin Statut\', name VARCHAR(191) NOT NULL, code VARCHAR(100) NOT NULL, priority INT NOT NULL, UNIQUE INDEX uniqueBesoinStatutCode (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant le statut d\'\'un besoin.\' ');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un category\', name VARCHAR(191) NOT NULL, code VARCHAR(100) NOT NULL, UNIQUE INDEX uniqueGroupCode (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant un Category.\' ');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un city\', name VARCHAR(191) NOT NULL, UNIQUE INDEX uniqueGroupCode (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant une adresse.\' ');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un commentaire/avis\', id_besoin INT DEFAULT NULL COMMENT \'Identifiant technique d\'\'un besoin\', company_id INT DEFAULT NULL COMMENT \'Identifiant technique d\'\'un company\', utilisateur_id INT DEFAULT NULL COMMENT \'Identifiant technique d\'\'un user\', author_company_id INT DEFAULT NULL COMMENT \'Identifiant technique d\'\'un company\', message VARCHAR(191) DEFAULT NULL, note NUMERIC(2, 1) NOT NULL, UNIQUE INDEX UNIQ_9474526CA72F5DFC (id_besoin), INDEX IDX_9474526C979B1AD6 (company_id), INDEX IDX_9474526CFB88E14F (utilisateur_id), INDEX IDX_9474526CD592666D (author_company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant un commentaire/avis.\' ');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un company\', id_address INT NOT NULL COMMENT \'Identifiant technique d\'\'un address\', id_category INT NOT NULL COMMENT \'Identifiant technique d\'\'un category\', id_company_statut INT NOT NULL COMMENT \'Identifiant technique d\'\'un CompanyStatut\', id_city INT NOT NULL COMMENT \'Identifiant technique d\'\'un city\', utilisateur_id INT NOT NULL COMMENT \'Identifiant technique d\'\'un user\', name VARCHAR(191) NOT NULL, num_siret VARCHAR(14) NOT NULL, url_name VARCHAR(100) NOT NULL, dt_created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, description_full VARCHAR(5000) DEFAULT NULL, description_clean VARCHAR(2000) DEFAULT NULL, image_profil VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_4FBF094FD3D3C6F1 (id_address), INDEX IDX_4FBF094F5697F554 (id_category), INDEX IDX_4FBF094F96856F23 (id_company_statut), INDEX IDX_4FBF094FA67B1E36 (id_city), INDEX IDX_4FBF094FFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant un Company.\' ');
        $this->addSql('CREATE TABLE company_sous_categories (company_id INT NOT NULL COMMENT \'Identifiant technique d\'\'un company\', id_company INT NOT NULL COMMENT \'Identifiant technique d\'\'une sous category\', INDEX IDX_937A29FF979B1AD6 (company_id), INDEX IDX_937A29FF9122A03F (id_company), PRIMARY KEY(company_id, id_company)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_statut (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un CompanyStatut\', name VARCHAR(191) NOT NULL, code VARCHAR(100) NOT NULL, UNIQUE INDEX uniqueCompanyStatutCode (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant le statut de l entreprise.\' ');
        $this->addSql('CREATE TABLE company_subscription (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique de la company_subscription\', id_subscription INT NOT NULL COMMENT \'Identifiant technique d\'\'un subscription\', id_company INT NOT NULL COMMENT \'Identifiant technique d\'\'un company\', dt_debut DATETIME DEFAULT NULL, dt_fin DATETIME DEFAULT NULL, INDEX IDX_5D0BAE1D800711A1 (id_subscription), INDEX IDX_5D0BAE1D9122A03F (id_company), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité pour définir la validité de l\'\'abonnement.\' ');
        $this->addSql('CREATE TABLE fonction (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique de la fonction\', code VARCHAR(50) NOT NULL COMMENT \'Code de la fonction\', description VARCHAR(191) NOT NULL COMMENT \'Nom de la fonction\', UNIQUE INDEX uniqueFonctionCode (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité technique regroupant les fonctions de l\'\'application. On associe une fonction à un rôle pour donner à ce rôle les droits sur cette fonction.\' ');
        $this->addSql('CREATE TABLE groupe (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un group\', name VARCHAR(50) NOT NULL COMMENT \'Nom du groupe\', code VARCHAR(191) NOT NULL COMMENT \'Code du groupe\', UNIQUE INDEX uniqueGroupCode (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité technique regroupant les rôles de l\'\'application. On associe un utilisateur à un groupe pour donner à cet utilisateur les droits du groupe.\' ');
        $this->addSql('CREATE TABLE groupes_roles (id_group INT NOT NULL COMMENT \'Identifiant technique d\'\'un group\', id_role INT NOT NULL COMMENT \'Identifiant technique d\'\'un role\', INDEX IDX_5F256D89834505F5 (id_group), INDEX IDX_5F256D89DC499668 (id_role), PRIMARY KEY(id_group, id_role)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notifiable_entity (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(255) NOT NULL, class VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notifiable_notification (id INT AUTO_INCREMENT NOT NULL, notifiable_entity_id INT DEFAULT NULL, notification_id INT DEFAULT NULL, seen TINYINT(1) NOT NULL, INDEX IDX_ADCFE0FAC3A0A4F8 (notifiable_entity_id), INDEX IDX_ADCFE0FAEF1A9D84 (notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, subject VARCHAR(4000) NOT NULL, message VARCHAR(4000) DEFAULT NULL, link VARCHAR(4000) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un mot de passe oublié\', id_utilisateur INT NOT NULL COMMENT \'Identifiant technique d\'\'un user\', expires_at DATETIME DEFAULT NULL, requested_at DATETIME DEFAULT NULL, selector VARCHAR(20) DEFAULT NULL, hashed_token VARCHAR(100) DEFAULT NULL, INDEX IDX_B9983CE550EAE44 (id_utilisateur), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant un mot de passe oublié.\' ');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un role\', name VARCHAR(191) NOT NULL, code VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant un Role.\' ');
        $this->addSql('CREATE TABLE roles_fontions (id_role INT NOT NULL COMMENT \'Identifiant technique d\'\'un role\', id_fonction INT NOT NULL COMMENT \'Identifiant technique de la fonction\', INDEX IDX_2D7645A5DC499668 (id_role), INDEX IDX_2D7645A559DB3928 (id_fonction), PRIMARY KEY(id_role, id_fonction)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sous_category (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'une sous category\', id_category INT NOT NULL COMMENT \'Identifiant technique d\'\'un category\', name VARCHAR(191) NOT NULL, code VARCHAR(100) NOT NULL, INDEX IDX_E022D945697F554 (id_category), UNIQUE INDEX uniqueSousCategoryCode (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant un SousCategory.\' ');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un subscription\', name VARCHAR(191) NOT NULL, code VARCHAR(100) NOT NULL, price INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant un Subscription.\' ');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL COMMENT \'Identifiant technique d\'\'un user\', username VARCHAR(191) NOT NULL, email VARCHAR(191) NOT NULL, first_name VARCHAR(191) NOT NULL, password VARCHAR(191) NOT NULL, last_name VARCHAR(191) NOT NULL, image_profil VARCHAR(100) DEFAULT NULL, updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX uniqueGroupCode (username, email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Entité représentant un user\' ');
        $this->addSql('CREATE TABLE utilisateurs_groupes (id_user INT NOT NULL COMMENT \'Identifiant technique d\'\'un user\', id_group INT NOT NULL COMMENT \'Identifiant technique d\'\'un group\', INDEX IDX_59950F8C6B3CA4B (id_user), INDEX IDX_59950F8C834505F5 (id_group), PRIMARY KEY(id_user, id_group)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25FE6EED44 FOREIGN KEY (besoin_id) REFERENCES besoin (id)');
        $this->addSql('ALTER TABLE besoin ADD CONSTRAINT FK_8118E8115697F554 FOREIGN KEY (id_category) REFERENCES category (id)');
        $this->addSql('ALTER TABLE besoin ADD CONSTRAINT FK_8118E8119B986D25 FOREIGN KEY (id_author) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE besoin ADD CONSTRAINT FK_8118E8119CB4C5D1 FOREIGN KEY (id_besoin_statut) REFERENCES besoin_statut (id)');
        $this->addSql('ALTER TABLE besoin ADD CONSTRAINT FK_8118E8119122A03F FOREIGN KEY (id_company) REFERENCES company (id)');
        $this->addSql('ALTER TABLE besoin ADD CONSTRAINT FK_8118E81163B4FF3A FOREIGN KEY (id_company_selected) REFERENCES company (id)');
        $this->addSql('ALTER TABLE besoin_sous_categories ADD CONSTRAINT FK_238FC76FE6EED44 FOREIGN KEY (besoin_id) REFERENCES besoin (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE besoin_sous_categories ADD CONSTRAINT FK_238FC76A72F5DFC FOREIGN KEY (id_besoin) REFERENCES sous_category (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA72F5DFC FOREIGN KEY (id_besoin) REFERENCES besoin (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CD592666D FOREIGN KEY (author_company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FD3D3C6F1 FOREIGN KEY (id_address) REFERENCES address (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F5697F554 FOREIGN KEY (id_category) REFERENCES category (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F96856F23 FOREIGN KEY (id_company_statut) REFERENCES company_statut (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FA67B1E36 FOREIGN KEY (id_city) REFERENCES city (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_sous_categories ADD CONSTRAINT FK_937A29FF979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_sous_categories ADD CONSTRAINT FK_937A29FF9122A03F FOREIGN KEY (id_company) REFERENCES sous_category (id)');
        $this->addSql('ALTER TABLE company_subscription ADD CONSTRAINT FK_5D0BAE1D800711A1 FOREIGN KEY (id_subscription) REFERENCES subscription (id)');
        $this->addSql('ALTER TABLE company_subscription ADD CONSTRAINT FK_5D0BAE1D9122A03F FOREIGN KEY (id_company) REFERENCES company (id)');
        $this->addSql('ALTER TABLE groupes_roles ADD CONSTRAINT FK_5F256D89834505F5 FOREIGN KEY (id_group) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE groupes_roles ADD CONSTRAINT FK_5F256D89DC499668 FOREIGN KEY (id_role) REFERENCES role (id)');
        $this->addSql('ALTER TABLE notifiable_notification ADD CONSTRAINT FK_ADCFE0FAC3A0A4F8 FOREIGN KEY (notifiable_entity_id) REFERENCES notifiable_entity (id)');
        $this->addSql('ALTER TABLE notifiable_notification ADD CONSTRAINT FK_ADCFE0FAEF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('ALTER TABLE reset_password ADD CONSTRAINT FK_B9983CE550EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE roles_fontions ADD CONSTRAINT FK_2D7645A5DC499668 FOREIGN KEY (id_role) REFERENCES role (id)');
        $this->addSql('ALTER TABLE roles_fontions ADD CONSTRAINT FK_2D7645A559DB3928 FOREIGN KEY (id_fonction) REFERENCES fonction (id)');
        $this->addSql('ALTER TABLE sous_category ADD CONSTRAINT FK_E022D945697F554 FOREIGN KEY (id_category) REFERENCES category (id)');
        $this->addSql('ALTER TABLE utilisateurs_groupes ADD CONSTRAINT FK_59950F8C6B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE utilisateurs_groupes ADD CONSTRAINT FK_59950F8C834505F5 FOREIGN KEY (id_group) REFERENCES groupe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FD3D3C6F1');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25FE6EED44');
        $this->addSql('ALTER TABLE besoin_sous_categories DROP FOREIGN KEY FK_238FC76FE6EED44');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA72F5DFC');
        $this->addSql('ALTER TABLE besoin DROP FOREIGN KEY FK_8118E8119CB4C5D1');
        $this->addSql('ALTER TABLE besoin DROP FOREIGN KEY FK_8118E8115697F554');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F5697F554');
        $this->addSql('ALTER TABLE sous_category DROP FOREIGN KEY FK_E022D945697F554');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FA67B1E36');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25979B1AD6');
        $this->addSql('ALTER TABLE besoin DROP FOREIGN KEY FK_8118E8119122A03F');
        $this->addSql('ALTER TABLE besoin DROP FOREIGN KEY FK_8118E81163B4FF3A');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C979B1AD6');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CD592666D');
        $this->addSql('ALTER TABLE company_sous_categories DROP FOREIGN KEY FK_937A29FF979B1AD6');
        $this->addSql('ALTER TABLE company_subscription DROP FOREIGN KEY FK_5D0BAE1D9122A03F');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F96856F23');
        $this->addSql('ALTER TABLE roles_fontions DROP FOREIGN KEY FK_2D7645A559DB3928');
        $this->addSql('ALTER TABLE groupes_roles DROP FOREIGN KEY FK_5F256D89834505F5');
        $this->addSql('ALTER TABLE utilisateurs_groupes DROP FOREIGN KEY FK_59950F8C834505F5');
        $this->addSql('ALTER TABLE notifiable_notification DROP FOREIGN KEY FK_ADCFE0FAC3A0A4F8');
        $this->addSql('ALTER TABLE notifiable_notification DROP FOREIGN KEY FK_ADCFE0FAEF1A9D84');
        $this->addSql('ALTER TABLE groupes_roles DROP FOREIGN KEY FK_5F256D89DC499668');
        $this->addSql('ALTER TABLE roles_fontions DROP FOREIGN KEY FK_2D7645A5DC499668');
        $this->addSql('ALTER TABLE besoin_sous_categories DROP FOREIGN KEY FK_238FC76A72F5DFC');
        $this->addSql('ALTER TABLE company_sous_categories DROP FOREIGN KEY FK_937A29FF9122A03F');
        $this->addSql('ALTER TABLE company_subscription DROP FOREIGN KEY FK_5D0BAE1D800711A1');
        $this->addSql('ALTER TABLE besoin DROP FOREIGN KEY FK_8118E8119B986D25');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CFB88E14F');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FFB88E14F');
        $this->addSql('ALTER TABLE reset_password DROP FOREIGN KEY FK_B9983CE550EAE44');
        $this->addSql('ALTER TABLE utilisateurs_groupes DROP FOREIGN KEY FK_59950F8C6B3CA4B');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE besoin');
        $this->addSql('DROP TABLE besoin_sous_categories');
        $this->addSql('DROP TABLE besoin_statut');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_sous_categories');
        $this->addSql('DROP TABLE company_statut');
        $this->addSql('DROP TABLE company_subscription');
        $this->addSql('DROP TABLE fonction');
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE groupes_roles');
        $this->addSql('DROP TABLE notifiable_entity');
        $this->addSql('DROP TABLE notifiable_notification');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE reset_password');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE roles_fontions');
        $this->addSql('DROP TABLE sous_category');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateurs_groupes');
    }
}
