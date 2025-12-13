<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251212144433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Foreign keys generation';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE elements ADD CONSTRAINT fk_elements_parts FOREIGN KEY (part_id) REFERENCES parts (id)');
        $this->addSql('ALTER TABLE elements ADD CONSTRAINT fk_elements_colors FOREIGN KEY (color_id) REFERENCES colors (id)');

        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT fk_notifications_identities FOREIGN KEY (identity_id) REFERENCES identities (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT fk_notifications_users FOREIGN KEY (user_id) REFERENCES users (id)');

        $this->addSql('ALTER TABLE set_elements ADD CONSTRAINT fk_set_elements_sets FOREIGN KEY (set_id) REFERENCES sets (id)');
        $this->addSql('ALTER TABLE set_elements ADD CONSTRAINT fk_set_elements_elements FOREIGN KEY (element_id) REFERENCES elements (id)');

        $this->addSql('ALTER TABLE user_elements ADD CONSTRAINT fk_user_elements_users FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_elements ADD CONSTRAINT fk_user_elements_elements FOREIGN KEY (element_id) REFERENCES elements (id)');

        $this->addSql('ALTER TABLE user_set_elements ADD CONSTRAINT fk_user_set_elements_user_sets FOREIGN KEY (user_set_id) REFERENCES user_sets (id)');
        $this->addSql('ALTER TABLE user_set_elements ADD CONSTRAINT fk_user_set_elements_elements FOREIGN KEY (element_id) REFERENCES elements (id)');

        $this->addSql('ALTER TABLE user_sets ADD CONSTRAINT fk_user_sets_sets FOREIGN KEY (set_id) REFERENCES sets (id)');
        $this->addSql('ALTER TABLE user_sets ADD CONSTRAINT fk_user_sets_users FOREIGN KEY (user_id) REFERENCES users (id)');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_sets DROP CONSTRAINT IF EXISTS fk_user_sets_users');
        $this->addSql('ALTER TABLE user_sets DROP CONSTRAINT IF EXISTS fk_user_sets_sets');
        $this->addSql('ALTER TABLE user_set_elements DROP CONSTRAINT IF EXISTS fk_user_set_elements_elements');
        $this->addSql('ALTER TABLE user_set_elements DROP CONSTRAINT IF EXISTS fk_user_set_elements_user_sets');
        $this->addSql('ALTER TABLE user_elements DROP CONSTRAINT IF EXISTS fk_user_elements_elements');
        $this->addSql('ALTER TABLE user_elements DROP CONSTRAINT IF EXISTS fk_user_elements_elements');
        $this->addSql('ALTER TABLE set_elements DROP CONSTRAINT IF EXISTS fk_set_elements_elements');
        $this->addSql('ALTER TABLE set_elements DROP CONSTRAINT IF EXISTS fk_set_elements_sets');
        $this->addSql('ALTER TABLE notifications DROP CONSTRAINT IF EXISTS fk_notifications_users');
        $this->addSql('ALTER TABLE notifications DROP CONSTRAINT IF EXISTS fk_notifications_identities');
        $this->addSql('ALTER TABLE elements DROP CONSTRAINT IF EXISTS fk_elements_parts');
        $this->addSql('ALTER TABLE elements DROP CONSTRAINT IF EXISTS fk_elements_parts');
    }
}
