<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20240909120827
 * 
 * The default database structure
 * 
 * @package DoctrineMigrations
 */
final class Version20240909120827 extends AbstractMigration
{
    /**
     * Get the description of this migration
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'The default database schema';
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
        $this->addSql('CREATE TABLE pastes (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, time TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
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
        $this->addSql('DROP TABLE pastes');
    }
}
