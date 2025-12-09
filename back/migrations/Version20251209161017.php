<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251209161017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE elements (id VARCHAR(36) NOT NULL, part_id VARCHAR(36) NOT NULL, color_id VARCHAR(36) NOT NULL, external_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, image_path VARCHAR(36) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_444A075D9F75D7B0 ON elements (external_id)');
        $this->addSql('ALTER TABLE user_sets ALTER status_date DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE elements');
        $this->addSql('ALTER TABLE user_sets ALTER status_date SET NOT NULL');
    }
}
