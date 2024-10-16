<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20241016085550
 * 
 * The migration for add browser information to pastes table
 * 
 * @package DoctrineMigrations
 */
final class Version20241016085550 extends AbstractMigration
{
    /**
     * Get the description of this migration
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add browser information to pastes table';
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
        $this->addSql('ALTER TABLE pastes ADD browser VARCHAR(255) NOT NULL');
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
        $this->addSql('ALTER TABLE pastes DROP browser');
    }
}
