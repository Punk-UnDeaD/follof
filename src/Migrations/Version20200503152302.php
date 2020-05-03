<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200503152302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE billing_team DROP CONSTRAINT FK_BB9298D8A76ED395');
        $this->addSql('ALTER TABLE billing_team ADD CONSTRAINT FK_BB9298D8A76ED395 FOREIGN KEY (user_id) REFERENCES user_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE billing_team DROP CONSTRAINT fk_bb9298d8a76ed395');
        $this->addSql('ALTER TABLE billing_team ADD CONSTRAINT fk_bb9298d8a76ed395 FOREIGN KEY (user_id) REFERENCES user_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
