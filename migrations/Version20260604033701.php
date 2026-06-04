<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260604033701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD current_status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B0D1B111 FOREIGN KEY (current_status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B0D1B111 ON user (current_status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B0D1B111');
        $this->addSql('DROP INDEX IDX_8D93D649B0D1B111 ON user');
        $this->addSql('ALTER TABLE user DROP current_status_id');
    }
}
