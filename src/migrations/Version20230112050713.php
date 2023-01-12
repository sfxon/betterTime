<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230112050713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE internal_stat DROP FOREIGN KEY FK_CC386EEAA76ED395');
        $this->addSql('ALTER TABLE internal_stat ADD CONSTRAINT FK_CC386EEAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE setting DROP FOREIGN KEY FK_9F74B898A76ED395');
        $this->addSql('ALTER TABLE setting ADD CONSTRAINT FK_9F74B898A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE time_tracking DROP FOREIGN KEY FK_CF921D0A76ED395');
        $this->addSql('ALTER TABLE time_tracking ADD CONSTRAINT FK_CF921D0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE internal_stat DROP FOREIGN KEY FK_CC386EEAA76ED395');
        $this->addSql('ALTER TABLE internal_stat ADD CONSTRAINT FK_CC386EEAA76ED395 FOREIGN KEY (user_id) REFERENCES internal_stat (id)');
        $this->addSql('ALTER TABLE setting DROP FOREIGN KEY FK_9F74B898A76ED395');
        $this->addSql('ALTER TABLE setting ADD CONSTRAINT FK_9F74B898A76ED395 FOREIGN KEY (user_id) REFERENCES setting (id)');
        $this->addSql('ALTER TABLE time_tracking DROP FOREIGN KEY FK_CF921D0A76ED395');
        $this->addSql('ALTER TABLE time_tracking ADD CONSTRAINT FK_CF921D0A76ED395 FOREIGN KEY (user_id) REFERENCES time_tracking (id)');
    }
}
