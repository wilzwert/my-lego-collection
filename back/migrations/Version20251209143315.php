<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251209143315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE colors (id VARCHAR(36) NOT NULL, external_id VARCHAR(255) NOT NULL, lego_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, rgb_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2BEC39F9F75D7B0 ON colors (external_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2BEC39F5814372C ON colors (lego_id)');
        $this->addSql('CREATE TABLE parts (id VARCHAR(36) NOT NULL, external_id VARCHAR(255) NOT NULL, lego_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, image_path VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6940A7FE9F75D7B0 ON parts (external_id)');
        $this->addSql('CREATE TABLE set_elements (id VARCHAR(36) NOT NULL, set_id VARCHAR(36) NOT NULL, element_id VARCHAR(36) NOT NULL, count INT NOT NULL, spare_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sets (id VARCHAR(36) NOT NULL, external_id VARCHAR(255) NOT NULL, lego_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, part_count INT NOT NULL, image_path VARCHAR(255) NOT NULL, production_year INT NOT NULL, creation_status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_948D45D19F75D7B0 ON sets (external_id)');
        $this->addSql('COMMENT ON COLUMN sets.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN sets.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_sets (id VARCHAR(36) NOT NULL, set_id VARCHAR(36) DEFAULT NULL, user_id VARCHAR(36) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, creation_status VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, status_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EE0C09188B8E8428 ON user_sets (created_at)');
        $this->addSql('CREATE INDEX IDX_EE0C091810FB0D18 ON user_sets (set_id)');
        $this->addSql('COMMENT ON COLUMN user_sets.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_sets.status_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_sets ADD CONSTRAINT FK_EE0C091810FB0D18 FOREIGN KEY (set_id) REFERENCES sets (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notifications ADD message_id VARCHAR(36) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_sets DROP CONSTRAINT FK_EE0C091810FB0D18');
        $this->addSql('DROP TABLE colors');
        $this->addSql('DROP TABLE parts');
        $this->addSql('DROP TABLE set_elements');
        $this->addSql('DROP TABLE sets');
        $this->addSql('DROP TABLE user_sets');
        $this->addSql('ALTER TABLE notifications DROP message_id');
    }
}
