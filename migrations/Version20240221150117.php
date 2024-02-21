<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221150117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album_format DROP FOREIGN KEY FK_CC14F681137ABCF');
        $this->addSql('ALTER TABLE album_format DROP FOREIGN KEY FK_CC14F68D629F605');
        $this->addSql('DROP TABLE format');
        $this->addSql('DROP TABLE album_format');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE format (id INT AUTO_INCREMENT NOT NULL, format VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE album_format (album_id INT NOT NULL, format_id INT NOT NULL, INDEX IDX_CC14F681137ABCF (album_id), INDEX IDX_CC14F68D629F605 (format_id), PRIMARY KEY(album_id, format_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE album_format ADD CONSTRAINT FK_CC14F681137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE album_format ADD CONSTRAINT FK_CC14F68D629F605 FOREIGN KEY (format_id) REFERENCES format (id) ON DELETE CASCADE');
    }
}
