<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231116145133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE situation ADD declarations_available_player LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD declarations_available_computer LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP declaration_available_player, DROP declaration_available_computer, CHANGE computer_card_played computer_card_played LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', CHANGE score score LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE situation ADD declaration_available_player LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD declaration_available_computer LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP declarations_available_player, DROP declarations_available_computer, CHANGE computer_card_played computer_card_played LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', CHANGE score score LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\'');
    }
}
