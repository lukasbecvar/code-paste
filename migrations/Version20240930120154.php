<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20240930120154
 * 
 * The migration for add index to pastes table
 * 
 * @package DoctrineMigrations
 */
final class Version20240930120154 extends AbstractMigration
{
    /**
     * Get the description of this migration
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add index to pastes table';
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
        $this->addSql('ALTER TABLE pastes ADD ip_address VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E54682055F37A13B ON pastes (token)');
        $this->addSql('CREATE INDEX pastes_token_idx ON pastes (token)');
        $this->addSql('CREATE INDEX pastes_ip_address_idx ON pastes (ip_address)');
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
        $this->addSql('DROP INDEX UNIQ_E54682055F37A13B ON pastes');
        $this->addSql('DROP INDEX pastes_token_idx ON pastes');
        $this->addSql('DROP INDEX pastes_ip_address_idx ON pastes');
        $this->addSql('ALTER TABLE pastes DROP ip_address');
    }
}
