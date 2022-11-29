<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221129182515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE track_keeping_entity (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(64) NOT NULL, technical_name VARCHAR(1024) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE track_keeping (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', track_keeping_entity_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', entry BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', count INT DEFAULT 0 NOT NULL, last_usage DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE track_keeping ADD CONSTRAINT FK_BETTERTIME_TKE_TK FOREIGN KEY (track_keeping_entity_id) REFERENCES track_keeping_entity (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE track_keeping');
        $this->addSql('DROP TABLE track_keeping_entity');
    }
}
