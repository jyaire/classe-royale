<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210106080426 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, classgroup_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_FBD8E0F84F695FC6 (classgroup_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE occupation (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, job_id INT NOT NULL, date_start DATETIME NOT NULL, date_end DATETIME DEFAULT NULL, salary INT NOT NULL, INDEX IDX_2F87D51CB944F1A (student_id), INDEX IDX_2F87D51BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F84F695FC6 FOREIGN KEY (classgroup_id) REFERENCES classgroup (id)');
        $this->addSql('ALTER TABLE occupation ADD CONSTRAINT FK_2F87D51CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE occupation ADD CONSTRAINT FK_2F87D51BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occupation DROP FOREIGN KEY FK_2F87D51BE04EA9');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE occupation');
    }
}
