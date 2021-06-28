<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210628135958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sentence (id INT AUTO_INCREMENT NOT NULL, sentence VARCHAR(255) NOT NULL, difficulty_level INT DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO sentence (id, sentence, difficulty_level, type) VALUES (NULL, "Un fromage qui pue", NULL, "start")');
        $this->addSql('INSERT INTO sentence (id, sentence, difficulty_level, type) VALUES (NULL, "Une jambe en mousse", NULL, "start")');
        $this->addSql('INSERT INTO sentence (id, sentence, difficulty_level, type) VALUES (NULL, "Un chat qui pete", NULL, "start")');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE sentence');
    }
}
