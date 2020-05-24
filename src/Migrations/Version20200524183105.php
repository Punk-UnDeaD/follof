<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200524183105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add numbers to members and voice menus/';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE billing_voice_menus ADD number VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE billing_voice_menus ADD CONSTRAINT FK_E4DD040996901F54 FOREIGN KEY (number) REFERENCES billing_numbers (number) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4DD040996901F54 ON billing_voice_menus (number)');
        $this->addSql('ALTER TABLE billing_members ADD number VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE billing_members ADD CONSTRAINT FK_2E8E592896901F54 FOREIGN KEY (number) REFERENCES billing_numbers (number) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2E8E592896901F54 ON billing_members (number)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE billing_members DROP CONSTRAINT FK_2E8E592896901F54');
        $this->addSql('DROP INDEX UNIQ_2E8E592896901F54');
        $this->addSql('ALTER TABLE billing_members DROP number');
        $this->addSql('ALTER TABLE billing_voice_menus DROP CONSTRAINT FK_E4DD040996901F54');
        $this->addSql('DROP INDEX UNIQ_E4DD040996901F54');
        $this->addSql('ALTER TABLE billing_voice_menus DROP number');
    }
}
