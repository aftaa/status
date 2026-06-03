<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260602115658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE task ADD status VARCHAR(20) NOT NULL DEFAULT 'not_completed'");
        $this->addSql("UPDATE task SET status = CASE WHEN is_completed = 1 THEN 'completed' ELSE 'not_completed' END");
        $this->addSql("ALTER TABLE task ALTER status DROP DEFAULT");
        $this->addSql("ALTER TABLE task DROP is_completed");    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE task ADD is_completed TINYINT(1) NOT NULL DEFAULT 0");
        $this->addSql("UPDATE task SET is_completed = CASE WHEN status = 'completed' THEN 1 ELSE 0 END");
        $this->addSql("ALTER TABLE task DROP status");
    }
}
