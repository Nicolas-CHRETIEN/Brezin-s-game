<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231129095209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE situation DROP FOREIGN KEY FK_EC2D9ACA9D86650F');
        $this->addSql('DROP INDEX UNIQ_EC2D9ACA9D86650F ON situation');
        $this->addSql('ALTER TABLE situation ADD user_id INT DEFAULT NULL, DROP user_id_id');
        $this->addSql('ALTER TABLE situation ADD CONSTRAINT FK_EC2D9ACAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC2D9ACAA76ED395 ON situation (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE situation DROP FOREIGN KEY FK_EC2D9ACAA76ED395');
        $this->addSql('DROP INDEX UNIQ_EC2D9ACAA76ED395 ON situation');
        $this->addSql('ALTER TABLE situation ADD user_id_id INT NOT NULL, DROP user_id');
        $this->addSql('ALTER TABLE situation ADD CONSTRAINT FK_EC2D9ACA9D86650F FOREIGN KEY (user_id_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC2D9ACA9D86650F ON situation (user_id_id)');
    }
}
