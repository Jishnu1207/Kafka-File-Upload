<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221213042320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(30) NOT NULL, record_count INT NOT NULL, status SMALLINT NOT NULL, uploaded_time DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE file_details');
        $this->addSql('DROP INDEX email ON contacts');
        $this->addSql('DROP INDEX file_id ON contacts');
        $this->addSql('ALTER TABLE contacts CHANGE modified_time modified_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file_details (file_id INT NOT NULL, file_name VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, record_count INT DEFAULT NULL, status INT DEFAULT NULL, uploaded_at DATETIME DEFAULT NULL, PRIMARY KEY(file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE file');
        $this->addSql('ALTER TABLE contacts CHANGE modified_date modified_time DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE contacts ADD CONSTRAINT contacts_ibfk_1 FOREIGN KEY (file_id) REFERENCES file_details (file_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX email ON contacts (email)');
        $this->addSql('CREATE INDEX file_id ON contacts (file_id)');
    }
}
