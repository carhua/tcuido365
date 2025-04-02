<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305110102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE estado (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, estado VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE estado');
        $this->addSql('ALTER TABLE accion DROP FOREIGN KEY FK_8A02E3B4B239FBC6');
        $this->addSql('ALTER TABLE accion DROP FOREIGN KEY FK_8A02E3B47E3C61F9');
        $this->addSql('DROP INDEX IDX_8A02E3B4B239FBC6 ON accion');
        $this->addSql('DROP INDEX IDX_8A02E3B47E3C61F9 ON accion');
        $this->addSql('CREATE INDEX codigo ON accion (codigo)');
        $this->addSql('ALTER TABLE institucion CHANGE region_id region_id INT DEFAULT NULL');
    }
}
