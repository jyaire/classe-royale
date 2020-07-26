<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200726210127 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE school (id INT AUTO_INCREMENT NOT NULL, director_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, postal_code INT NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, rne VARCHAR(255) NOT NULL, date_create DATETIME NOT NULL, date_modif DATETIME DEFAULT NULL, INDEX IDX_F99EDABB899FB366 (director_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, abbreviation VARCHAR(255) NOT NULL, rank INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, classgroup_id INT DEFAULT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, ine VARCHAR(255) NOT NULL, is_girl TINYINT(1) NOT NULL, date_create DATETIME NOT NULL, date_modif DATETIME DEFAULT NULL, is_lead TINYINT(1) NOT NULL, xp INT NOT NULL, gold INT NOT NULL, elixir INT NOT NULL, INDEX IDX_B723AF334F695FC6 (classgroup_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classgroup (id INT AUTO_INCREMENT NOT NULL, school_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, ref VARCHAR(255) DEFAULT NULL, INDEX IDX_84980391C32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE school ADD CONSTRAINT FK_F99EDABB899FB366 FOREIGN KEY (director_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF334F695FC6 FOREIGN KEY (classgroup_id) REFERENCES classgroup (id)');
        $this->addSql('ALTER TABLE classgroup ADD CONSTRAINT FK_84980391C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE classgroup DROP FOREIGN KEY FK_84980391C32A47EE');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF334F695FC6');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE classgroup');
    }
}
