<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210702171207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO sentence (id, sentence, difficulty_level, type) VALUES (NULL, "Qui a choisi la phrase la plus compliquée ?", NULL, "vote")');
        $this->addSql('INSERT INTO sentence (id, sentence, difficulty_level, type) VALUES (NULL, "Quel récapitulatif est le plus juste à la fin ?", NULL, "vote")');
        $this->addSql('INSERT INTO sentence (id, sentence, difficulty_level, type) VALUES (NULL, "Quel récapitulatif a l\'évolution la plus pourrie ?", NULL, "vote")');
        $this->addSql('INSERT INTO sentence (id, sentence, difficulty_level, type) VALUES (NULL, "Qui est le meilleur dessinateur?", NULL, "vote")');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE sentence');
    }
}