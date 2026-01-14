<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260114125157 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE to_do_lists(
            to_do_id UUID PRIMARY KEY,
            user_id UUID NOT NULL,
            title VARCHAR(255) NOT NULL,
            mode VARCHAR(50) NOT NULL,
            timer_type VARCHAR(50) NOT NULL,
            timer_value INT NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            CONSTRAINT fk_todo_lists_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )');

        $this->addSql('CREATE INDEX idx_to_do_lists_user_id ON to_do_lists(user_id)');
        $this->addSql('CREATE INDEX idx_to_do_lists_mode ON to_do_lists(mode)');
        $this->addSql('CREATE INDEX idx_to_do_lists_timer_type ON to_do_lists(timer_type)');

        $this->addSql('CREATE TABLE to_do_history (
            to_do_id_history UUID PRIMARY KEY,
            to_do_id UUID NOT NULL,
            processed_time INTEGER NOT NULL,
            processed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            CONSTRAINT fk_to_do_history_todo FOREIGN KEY (to_do_id) REFERENCES to_do_lists(to_do_id) ON DELETE CASCADE
        )');

        // Index pour l'historique
        $this->addSql('CREATE INDEX idx_to_do_history_to_do_id ON to_do_history(to_do_id)');
        $this->addSql('CREATE INDEX idx_to_do_history_processed_at ON to_do_history(processed_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS to_do_history');
        $this->addSql('DROP TABLE IF EXISTS to_do_lists');
    }
}
