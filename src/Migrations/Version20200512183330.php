<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200512183330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE billing_voice_menus (id UUID NOT NULL, team_id UUID DEFAULT NULL, data JSON DEFAULT \'{}\' NOT NULL, file VARCHAR(255) DEFAULT NULL, status VARCHAR(16) NOT NULL, internal_number_number VARCHAR(16) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E4DD0409296CD8AE ON billing_voice_menus (team_id)');
        $this->addSql('COMMENT ON COLUMN billing_voice_menus.id IS \'(DC2Type:billing_guid)\'');
        $this->addSql('COMMENT ON COLUMN billing_voice_menus.team_id IS \'(DC2Type:billing_guid)\'');
        $this->addSql('ALTER TABLE billing_voice_menus ADD CONSTRAINT FK_E4DD0409296CD8AE FOREIGN KEY (team_id) REFERENCES billing_team (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE billing_members ADD data JSON DEFAULT \'{}\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE billing_voice_menus');
        $this->addSql('ALTER TABLE billing_members DROP data');
    }
}
