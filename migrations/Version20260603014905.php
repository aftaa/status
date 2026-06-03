<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260603000001 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO status (name, slug, color, bg_color, created_at) VALUES
            ('Свободен', 'svoboden', '#2D8B2D', '#E8F5E9', NOW()),
            ('Занят', 'zanyat', '#C62828', '#FFEBEE', NOW()),
            ('Сплю', 'splyu', '#5C6BC0', '#E8EAF6', NOW()),
            ('На связи', 'na-svyazi', '#F9A825', '#FFF8E1', NOW()),
            ('Не беспокоить', 'ne-bespokoit', '#6D4C41', '#EFEBE9', NOW())
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM status WHERE slug IN ('svoboden', 'zanyat', 'splyu', 'na-svyazi', 'ne-bespokoit')");
    }
}
