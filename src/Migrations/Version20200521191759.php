<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200521191759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Billing numbers';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE billing_numbers (number VARCHAR(16) NOT NULL, team_id UUID DEFAULT NULL, PRIMARY KEY(number))');
        $this->addSql('CREATE INDEX IDX_1CB0412E296CD8AE ON billing_numbers (team_id)');
        $this->addSql('COMMENT ON COLUMN billing_numbers.team_id IS \'(DC2Type:billing_guid)\'');
        $this->addSql('ALTER TABLE billing_numbers ADD CONSTRAINT FK_1CB0412E296CD8AE FOREIGN KEY (team_id) REFERENCES billing_team (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE billing_numbers');
    }
}
