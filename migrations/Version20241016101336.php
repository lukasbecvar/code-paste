<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20241016101336
 * 
 * The migration for add views column to paste table
 * 
 * @package DoctrineMigrations
 */
final class Version20241016101336 extends AbstractMigration
{
    /**
     * Get the description of this migration
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add views column to paste table';
    }

    /**
     * Execute the migration
     *
     * @param Schema $schema
     * 
     * @return void
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE pastes ADD views INT NOT NULL');
    }

    /**
     * Undo the migration
     *
     * @param Schema $schema
     * 
     * @return void
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE pastes DROP views');
    }
}
