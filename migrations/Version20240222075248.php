<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222075248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tracklist (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, duration VARCHAR(255) DEFAULT NULL, id_album_id INT NOT NULL, INDEX IDX_8DFC4B6741EC475A (id_album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE tracklist ADD CONSTRAINT FK_8DFC4B6741EC475A FOREIGN KEY (id_album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE album CHANGE id id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tracklist DROP FOREIGN KEY FK_8DFC4B6741EC475A');
        $this->addSql('DROP TABLE tracklist');
        $this->addSql('ALTER TABLE album CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
