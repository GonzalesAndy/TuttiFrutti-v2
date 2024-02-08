<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208135606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, artist VARCHAR(100) NOT NULL, year INT NOT NULL, country VARCHAR(100) NOT NULL, cover_image VARCHAR(510) DEFAULT NULL, discog_link VARCHAR(510) NOT NULL, likes INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE album_format (album_id INT NOT NULL, format_id INT NOT NULL, INDEX IDX_CC14F681137ABCF (album_id), INDEX IDX_CC14F68D629F605 (format_id), PRIMARY KEY(album_id, format_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE format (id INT AUTO_INCREMENT NOT NULL, format VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, genre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE style (id INT AUTO_INCREMENT NOT NULL, style VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE album_format ADD CONSTRAINT FK_CC14F681137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE album_format ADD CONSTRAINT FK_CC14F68D629F605 FOREIGN KEY (format_id) REFERENCES format (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album_format DROP FOREIGN KEY FK_CC14F681137ABCF');
        $this->addSql('ALTER TABLE album_format DROP FOREIGN KEY FK_CC14F68D629F605');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE album_format');
        $this->addSql('DROP TABLE format');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE style');
    }
}
