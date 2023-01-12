<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230112041531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE internal_stat DROP FOREIGN KEY FK_BETTERTIME_TKE_TK');
        $this->addSql('ALTER TABLE internal_stat ADD user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE internal_stat_entity_id internal_stat_entity_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE count count INT NOT NULL');
        $this->addSql('ALTER TABLE internal_stat ADD CONSTRAINT FK_CC386EEAA76ED395 FOREIGN KEY (user_id) REFERENCES internal_stat (id)');
        $this->addSql('CREATE INDEX IDX_CC386EEAA76ED395 ON internal_stat (user_id)');
        $this->addSql('DROP INDEX fk_bettertime_tke_tk ON internal_stat');
        $this->addSql('CREATE INDEX IDX_CC386EEA254DA6F9 ON internal_stat (internal_stat_entity_id)');
        $this->addSql('ALTER TABLE internal_stat ADD CONSTRAINT FK_BETTERTIME_TKE_TK FOREIGN KEY (internal_stat_entity_id) REFERENCES internal_stat_entity (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE internal_stat DROP FOREIGN KEY FK_CC386EEAA76ED395');
        $this->addSql('DROP INDEX IDX_CC386EEAA76ED395 ON internal_stat');
        $this->addSql('ALTER TABLE internal_stat DROP FOREIGN KEY FK_CC386EEA254DA6F9');
        $this->addSql('ALTER TABLE internal_stat DROP user_id, CHANGE internal_stat_entity_id internal_stat_entity_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE count count INT DEFAULT 0 NOT NULL');
        $this->addSql('DROP INDEX idx_cc386eea254da6f9 ON internal_stat');
        $this->addSql('CREATE INDEX FK_BETTERTIME_TKE_TK ON internal_stat (internal_stat_entity_id)');
        $this->addSql('ALTER TABLE internal_stat ADD CONSTRAINT FK_CC386EEA254DA6F9 FOREIGN KEY (internal_stat_entity_id) REFERENCES internal_stat_entity (id)');
    }
}
