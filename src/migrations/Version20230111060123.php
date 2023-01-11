<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230111060123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_tracking ADD user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE time_tracking ADD CONSTRAINT FK_CF921D0A76ED395 FOREIGN KEY (user_id) REFERENCES time_tracking (id)');
        $this->addSql('CREATE INDEX IDX_CF921D0A76ED395 ON time_tracking (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_tracking DROP FOREIGN KEY FK_CF921D0A76ED395');
        $this->addSql('DROP INDEX IDX_CF921D0A76ED395 ON time_tracking');
        $this->addSql('ALTER TABLE time_tracking DROP user_id');
    }
}
