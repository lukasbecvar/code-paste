<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20241202122152
 * 
 * Migration for change time column to datetime in paste table
 * 
 * @package DoctrineMigrations
 */
final class Version20241202122152 extends AbstractMigration
{
    /**
     * Get the description of this migration
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Migration for change time column to datetime in paste table';
    }

    /**
     * Execute migration
     *
     * @param Schema $schema
     * 
     * @return void
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE pastes CHANGE time time DATETIME NOT NULL');
    }

    /**
     * Undo migration
     *
     * @param Schema $schema
     * 
     * @return void
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE pastes CHANGE time time TIME NOT NULL');
    }
}
