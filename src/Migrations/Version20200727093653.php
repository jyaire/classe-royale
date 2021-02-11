<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200727093653 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reason (id INT AUTO_INCREMENT NOT NULL, sentence VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_user (team_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5C722232296CD8AE (team_id), INDEX IDX_5C722232A76ED395 (user_id), PRIMARY KEY(team_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, property VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_user (student_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B2B0AD91CB944F1A (student_id), INDEX IDX_B2B0AD91A76ED395 (user_id), PRIMARY KEY(student_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classgroup_user (classgroup_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_828AB31B4F695FC6 (classgroup_id), INDEX IDX_828AB31BA76ED395 (user_id), PRIMARY KEY(classgroup_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classgroup_section (classgroup_id INT NOT NULL, section_id INT NOT NULL, INDEX IDX_3EAAB71C4F695FC6 (classgroup_id), INDEX IDX_3EAAB71CD823E37A (section_id), PRIMARY KEY(classgroup_id, section_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, rank INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card (id INT AUTO_INCREMENT NOT NULL, subject_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, level INT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_161498D323EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE point (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, reason_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, quantity INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_B7A5F324CB944F1A (student_id), INDEX IDX_B7A5F32459BB1592 (reason_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_user ADD CONSTRAINT FK_B2B0AD91CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_user ADD CONSTRAINT FK_B2B0AD91A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classgroup_user ADD CONSTRAINT FK_828AB31B4F695FC6 FOREIGN KEY (classgroup_id) REFERENCES classgroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classgroup_user ADD CONSTRAINT FK_828AB31BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classgroup_section ADD CONSTRAINT FK_3EAAB71C4F695FC6 FOREIGN KEY (classgroup_id) REFERENCES classgroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classgroup_section ADD CONSTRAINT FK_3EAAB71CD823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D323EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F324CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F32459BB1592 FOREIGN KEY (reason_id) REFERENCES reason (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F32459BB1592');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232296CD8AE');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D323EDC87');
        $this->addSql('DROP TABLE reason');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_user');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('DROP TABLE student_user');
        $this->addSql('DROP TABLE classgroup_user');
        $this->addSql('DROP TABLE classgroup_section');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE point');
    }
}
