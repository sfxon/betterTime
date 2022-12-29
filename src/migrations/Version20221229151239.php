<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221229151239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE config_definition (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', technical_name VARCHAR(255) NOT NULL, levels LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', description VARCHAR(4096) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE config_value (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', config_definition_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', level VARCHAR(16) NOT NULL, foreign_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', value LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE config_definition');
        $this->addSql('DROP TABLE config_value');
    }
}