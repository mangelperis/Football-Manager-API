<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240412221035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clubs (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, shortname VARCHAR(3) NOT NULL, country VARCHAR(2) NOT NULL, budget DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX budget_index (budget), UNIQUE INDEX name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coaches (id INT AUTO_INCREMENT NOT NULL, club_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, salary DOUBLE PRECISION NOT NULL, email VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, role VARCHAR(20) NOT NULL, INDEX IDX_C413176561190A32 (club_id), INDEX name_index (name), UNIQUE INDEX email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE players (id INT AUTO_INCREMENT NOT NULL, club_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, salary DOUBLE PRECISION NOT NULL, email VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, position VARCHAR(20) NOT NULL, INDEX IDX_264E43A661190A32 (club_id), INDEX name_index (name), UNIQUE INDEX email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coaches ADD CONSTRAINT FK_C413176561190A32 FOREIGN KEY (club_id) REFERENCES clubs (id)');
        $this->addSql('ALTER TABLE players ADD CONSTRAINT FK_264E43A661190A32 FOREIGN KEY (club_id) REFERENCES clubs (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coaches DROP FOREIGN KEY FK_C413176561190A32');
        $this->addSql('ALTER TABLE players DROP FOREIGN KEY FK_264E43A661190A32');
        $this->addSql('DROP TABLE clubs');
        $this->addSql('DROP TABLE coaches');
        $this->addSql('DROP TABLE players');
    }
}
