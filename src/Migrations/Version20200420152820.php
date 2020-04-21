<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200420152820 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE billing_members (id UUID NOT NULL, team_id UUID DEFAULT NULL, login VARCHAR(25) DEFAULT NULL, email VARCHAR(128) DEFAULT NULL, password_hash VARCHAR(255) DEFAULT NULL, role VARCHAR(32) NOT NULL, status VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2E8E5928296CD8AE ON billing_members (team_id)');
        $this->addSql('COMMENT ON COLUMN billing_members.id IS \'(DC2Type:billing_id)\'');
        $this->addSql('COMMENT ON COLUMN billing_members.team_id IS \'(DC2Type:billing_id)\'');
        $this->addSql('COMMENT ON COLUMN billing_members.role IS \'(DC2Type:billing_member_role)\'');
        $this->addSql('CREATE TABLE billing_team (id UUID NOT NULL, owner_id UUID DEFAULT NULL, user_id UUID NOT NULL, billing_id VARCHAR(255) NOT NULL, ballance_value NUMERIC(8, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BB9298D87E3C61F9 ON billing_team (owner_id)');
        $this->addSql('CREATE INDEX IDX_BB9298D8A76ED395 ON billing_team (user_id)');
        $this->addSql('COMMENT ON COLUMN billing_team.id IS \'(DC2Type:billing_id)\'');
        $this->addSql('COMMENT ON COLUMN billing_team.owner_id IS \'(DC2Type:billing_id)\'');
        $this->addSql('COMMENT ON COLUMN billing_team.user_id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('ALTER TABLE billing_members ADD CONSTRAINT FK_2E8E5928296CD8AE FOREIGN KEY (team_id) REFERENCES billing_team (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE billing_team ADD CONSTRAINT FK_BB9298D87E3C61F9 FOREIGN KEY (owner_id) REFERENCES billing_members (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE billing_team ADD CONSTRAINT FK_BB9298D8A76ED395 FOREIGN KEY (user_id) REFERENCES user_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE billing_team DROP CONSTRAINT FK_BB9298D87E3C61F9');
        $this->addSql('ALTER TABLE billing_members DROP CONSTRAINT FK_2E8E5928296CD8AE');
        $this->addSql('DROP TABLE billing_members');
        $this->addSql('DROP TABLE billing_team');
    }
}
