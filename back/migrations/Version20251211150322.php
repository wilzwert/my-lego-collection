<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211150322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_set_elements (id VARCHAR(36) NOT NULL, user_set_id VARCHAR(36) NOT NULL, element_id VARCHAR(36) NOT NULL, count INT NOT NULL, spare_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE user_sets DROP CONSTRAINT fk_ee0c091810fb0d18');
        $this->addSql('ALTER TABLE user_sets ALTER set_id SET NOT NULL');
        $this->addSql('CREATE INDEX IDX_EE0C0918A76ED395 ON user_sets (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_set_elements');
        $this->addSql('DROP INDEX IDX_EE0C0918A76ED395');
        $this->addSql('ALTER TABLE user_sets ALTER set_id DROP NOT NULL');
        $this->addSql('ALTER TABLE user_sets ADD CONSTRAINT fk_ee0c091810fb0d18 FOREIGN KEY (set_id) REFERENCES sets (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
