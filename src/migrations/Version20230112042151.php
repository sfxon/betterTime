<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230112042151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_58df0651e7927c74 ON admin');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D76E7927C74 ON admin (email)');
        $this->addSql('ALTER TABLE setting ADD user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE value value VARCHAR(65535) NOT NULL');
        $this->addSql('ALTER TABLE setting ADD CONSTRAINT FK_9F74B898A76ED395 FOREIGN KEY (user_id) REFERENCES setting (id)');
        $this->addSql('CREATE INDEX IDX_9F74B898A76ED395 ON setting (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_880e0d76e7927c74 ON admin');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_58DF0651E7927C74 ON admin (email)');
        $this->addSql('ALTER TABLE setting DROP FOREIGN KEY FK_9F74B898A76ED395');
        $this->addSql('DROP INDEX IDX_9F74B898A76ED395 ON setting');
        $this->addSql('ALTER TABLE setting DROP user_id, CHANGE value value MEDIUMTEXT NOT NULL');
    }
}
