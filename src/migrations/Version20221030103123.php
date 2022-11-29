<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221030103123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE project (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE time_tracking (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', project_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', starttime DATETIME NOT NULL, endtime DATETIME DEFAULT NULL, use_on_invoice TINYINT(1) NOT NULL, invoice_id INT DEFAULT NULL, comment VARCHAR(4096) DEFAULT NULL, INDEX IDX_CF921D0166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE time_tracking ADD CONSTRAINT FK_CF921D0166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_tracking DROP FOREIGN KEY FK_CF921D0166D1F9C');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE time_tracking');
    }
}
