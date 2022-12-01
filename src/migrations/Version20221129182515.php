<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20221129182515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE internal_stat_entity (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(64) NOT NULL, technical_name VARCHAR(1024) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE internal_stat (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', internal_stat_entity_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', entry BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', count INT DEFAULT 0 NOT NULL, last_usage DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE internal_stat ADD CONSTRAINT FK_BETTERTIME_TKE_TK FOREIGN KEY (internal_stat_entity_id) REFERENCES internal_stat_entity (id)');

        // Add required data
        $this->addSql('INSERT INTO internal_stat_entity (id, name, technical_name) VALUES (UUID(), \'Projekte\', \'project\')');
        $this->addSql('INSERT INTO internal_stat_entity (id, name, technical_name) VALUES (UUID(), \'Projekt-Zeiten\', \'timeTracking\')');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE internal_stat');
        $this->addSql('DROP TABLE internal_stat_entity');
    }
}
