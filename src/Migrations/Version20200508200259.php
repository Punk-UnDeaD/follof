<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200508200259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE billing_voice_menus ALTER data TYPE JSON');
        $this->addSql('ALTER TABLE billing_voice_menus ALTER data DROP DEFAULT');
        $this->addSql('ALTER TABLE billing_voice_menus ALTER file DROP NOT NULL');
        $this->addSql('ALTER TABLE billing_voice_menus ALTER internal_number_number DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE billing_voice_menus ALTER data TYPE JSON');
        $this->addSql('ALTER TABLE billing_voice_menus ALTER data DROP DEFAULT');
        $this->addSql('ALTER TABLE billing_voice_menus ALTER file SET NOT NULL');
        $this->addSql('ALTER TABLE billing_voice_menus ALTER internal_number_number SET NOT NULL');
    }
}
