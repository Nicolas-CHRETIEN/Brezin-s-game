<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231116140717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, winner VARCHAR(255) NOT NULL, score_player VARCHAR(255) NOT NULL, score_computer VARCHAR(255) NOT NULL, declarations_made_player LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', declarations_made_computer LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', valuables_cards_in_tricks_player VARCHAR(255) NOT NULL, valuables_cards_in_tricks_computer VARCHAR(255) NOT NULL, rounds_number VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_FF232B319D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, introduction VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B319D86650F FOREIGN KEY (user_id_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cards CHANGE number number VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE situation ADD user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE situation ADD CONSTRAINT FK_EC2D9ACA9D86650F FOREIGN KEY (user_id_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC2D9ACA9D86650F ON situation (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE situation DROP FOREIGN KEY FK_EC2D9ACA9D86650F');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B319D86650F');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE cards CHANGE number number NUMERIC(64, 0) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_EC2D9ACA9D86650F ON situation');
        $this->addSql('ALTER TABLE situation DROP user_id_id');
    }
}
