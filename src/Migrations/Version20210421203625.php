<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210421203625 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE card (id INT AUTO_INCREMENT NOT NULL, subject_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, level INT NOT NULL, type VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_161498D323EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card_student (card_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_E03925454ACC9A20 (card_id), INDEX IDX_E0392545CB944F1A (student_id), PRIMARY KEY(card_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classgroup (id INT AUTO_INCREMENT NOT NULL, school_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, ref VARCHAR(255) DEFAULT NULL, invit VARCHAR(255) DEFAULT NULL, INDEX IDX_84980391C32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classgroup_user (classgroup_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_828AB31B4F695FC6 (classgroup_id), INDEX IDX_828AB31BA76ED395 (user_id), PRIMARY KEY(classgroup_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classgroup_section (classgroup_id INT NOT NULL, section_id INT NOT NULL, INDEX IDX_3EAAB71C4F695FC6 (classgroup_id), INDEX IDX_3EAAB71CD823E37A (section_id), PRIMARY KEY(classgroup_id, section_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, classgroup_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_FBD8E0F84F695FC6 (classgroup_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE level (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, xp INT NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE occupation (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, job_id INT NOT NULL, date_start DATETIME NOT NULL, date_end DATETIME DEFAULT NULL, salary INT NOT NULL, INDEX IDX_2F87D51CB944F1A (student_id), INDEX IDX_2F87D51BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, property VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE point (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, reason_id INT DEFAULT NULL, author_id INT DEFAULT NULL, purchase_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, quantity INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_B7A5F324CB944F1A (student_id), INDEX IDX_B7A5F32459BB1592 (reason_id), INDEX IDX_B7A5F324F675F31B (author_id), INDEX IDX_B7A5F324558FBEB9 (purchase_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, classgroup_id INT DEFAULT NULL, creator_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price INT NOT NULL, currency VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, INDEX IDX_D34A04AD4F695FC6 (classgroup_id), INDEX IDX_D34A04AD61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, product_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_6117D13BCB944F1A (student_id), INDEX IDX_6117D13B4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reason (id INT AUTO_INCREMENT NOT NULL, sentence VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE school (id INT AUTO_INCREMENT NOT NULL, director_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, postal_code INT NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, rne VARCHAR(255) NOT NULL, date_create DATETIME NOT NULL, date_modif DATETIME DEFAULT NULL, INDEX IDX_F99EDABB899FB366 (director_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, abbreviation VARCHAR(255) NOT NULL, `rank` INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, classgroup_id INT DEFAULT NULL, section_id INT DEFAULT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, ine VARCHAR(255) DEFAULT NULL, is_girl TINYINT(1) NOT NULL, date_create DATETIME NOT NULL, date_modif DATETIME DEFAULT NULL, is_lead TINYINT(1) NOT NULL, xp INT NOT NULL, gold INT NOT NULL, elixir INT NOT NULL, birthdate DATE DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, invit VARCHAR(255) DEFAULT NULL, INDEX IDX_B723AF334F695FC6 (classgroup_id), INDEX IDX_B723AF33D823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_user (student_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B2B0AD91CB944F1A (student_id), INDEX IDX_B2B0AD91A76ED395 (user_id), PRIMARY KEY(student_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, `rank` INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE support (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, is_parent TINYINT(1) NOT NULL, parent INT DEFAULT NULL, `rank` INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, classgroup_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C4E0A61F4F695FC6 (classgroup_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_student (team_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_485ADE04296CD8AE (team_id), INDEX IDX_485ADE04CB944F1A (student_id), PRIMARY KEY(team_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, is_woman TINYINT(1) NOT NULL, date_create DATETIME NOT NULL, date_modif DATETIME DEFAULT NULL, is_verified TINYINT(1) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D323EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE card_student ADD CONSTRAINT FK_E03925454ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card_student ADD CONSTRAINT FK_E0392545CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classgroup ADD CONSTRAINT FK_84980391C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE classgroup_user ADD CONSTRAINT FK_828AB31B4F695FC6 FOREIGN KEY (classgroup_id) REFERENCES classgroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classgroup_user ADD CONSTRAINT FK_828AB31BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classgroup_section ADD CONSTRAINT FK_3EAAB71C4F695FC6 FOREIGN KEY (classgroup_id) REFERENCES classgroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classgroup_section ADD CONSTRAINT FK_3EAAB71CD823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F84F695FC6 FOREIGN KEY (classgroup_id) REFERENCES classgroup (id)');
        $this->addSql('ALTER TABLE occupation ADD CONSTRAINT FK_2F87D51CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE occupation ADD CONSTRAINT FK_2F87D51BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F324CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F32459BB1592 FOREIGN KEY (reason_id) REFERENCES reason (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F324F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F324558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD4F695FC6 FOREIGN KEY (classgroup_id) REFERENCES classgroup (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BCB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE school ADD CONSTRAINT FK_F99EDABB899FB366 FOREIGN KEY (director_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF334F695FC6 FOREIGN KEY (classgroup_id) REFERENCES classgroup (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE student_user ADD CONSTRAINT FK_B2B0AD91CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_user ADD CONSTRAINT FK_B2B0AD91A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F4F695FC6 FOREIGN KEY (classgroup_id) REFERENCES classgroup (id)');
        $this->addSql('ALTER TABLE team_student ADD CONSTRAINT FK_485ADE04296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_student ADD CONSTRAINT FK_485ADE04CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_student DROP FOREIGN KEY FK_E03925454ACC9A20');
        $this->addSql('ALTER TABLE classgroup_user DROP FOREIGN KEY FK_828AB31B4F695FC6');
        $this->addSql('ALTER TABLE classgroup_section DROP FOREIGN KEY FK_3EAAB71C4F695FC6');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F84F695FC6');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD4F695FC6');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF334F695FC6');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F4F695FC6');
        $this->addSql('ALTER TABLE occupation DROP FOREIGN KEY FK_2F87D51BE04EA9');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B4584665A');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F324558FBEB9');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F32459BB1592');
        $this->addSql('ALTER TABLE classgroup DROP FOREIGN KEY FK_84980391C32A47EE');
        $this->addSql('ALTER TABLE classgroup_section DROP FOREIGN KEY FK_3EAAB71CD823E37A');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33D823E37A');
        $this->addSql('ALTER TABLE card_student DROP FOREIGN KEY FK_E0392545CB944F1A');
        $this->addSql('ALTER TABLE occupation DROP FOREIGN KEY FK_2F87D51CB944F1A');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F324CB944F1A');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BCB944F1A');
        $this->addSql('ALTER TABLE student_user DROP FOREIGN KEY FK_B2B0AD91CB944F1A');
        $this->addSql('ALTER TABLE team_student DROP FOREIGN KEY FK_485ADE04CB944F1A');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D323EDC87');
        $this->addSql('ALTER TABLE team_student DROP FOREIGN KEY FK_485ADE04296CD8AE');
        $this->addSql('ALTER TABLE classgroup_user DROP FOREIGN KEY FK_828AB31BA76ED395');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F324F675F31B');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD61220EA6');
        $this->addSql('ALTER TABLE school DROP FOREIGN KEY FK_F99EDABB899FB366');
        $this->addSql('ALTER TABLE student_user DROP FOREIGN KEY FK_B2B0AD91A76ED395');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE card_student');
        $this->addSql('DROP TABLE classgroup');
        $this->addSql('DROP TABLE classgroup_user');
        $this->addSql('DROP TABLE classgroup_section');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE occupation');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('DROP TABLE point');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('DROP TABLE reason');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE student_user');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE support');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_student');
        $this->addSql('DROP TABLE user');
    }
}
