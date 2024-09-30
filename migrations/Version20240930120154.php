<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240930120154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pastes ADD ip_address VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E54682055F37A13B ON pastes (token)');
        $this->addSql('CREATE INDEX pastes_token_idx ON pastes (token)');
        $this->addSql('CREATE INDEX pastes_ip_address_idx ON pastes (ip_address)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_E54682055F37A13B ON pastes');
        $this->addSql('DROP INDEX pastes_token_idx ON pastes');
        $this->addSql('DROP INDEX pastes_ip_address_idx ON pastes');
        $this->addSql('ALTER TABLE pastes DROP ip_address');
    }
}
