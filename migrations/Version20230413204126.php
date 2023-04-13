<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230413204126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_F4B98442989D9B62 ON main_page');
        $this->addSql('ALTER TABLE main_page DROP slug, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE keywords keywords VARCHAR(255) DEFAULT NULL, CHANGE head_title head_title VARCHAR(255) DEFAULT NULL, CHANGE show_comments show_comments TINYINT(1) DEFAULT 0 NOT NULL, CHANGE show_articles show_articles TINYINT(1) DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE main_page ADD slug VARCHAR(100) NOT NULL, CHANGE title title VARCHAR(255) NOT NULL, CHANGE head_title head_title VARCHAR(255) NOT NULL, CHANGE keywords keywords VARCHAR(255) NOT NULL, CHANGE show_comments show_comments TINYINT(1) NOT NULL, CHANGE show_articles show_articles TINYINT(1) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4B98442989D9B62 ON main_page (slug)');
    }
}
