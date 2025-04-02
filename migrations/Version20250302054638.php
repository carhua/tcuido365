<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302054638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE instituciones (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, region_id INT NOT NULL, provincia_id INT DEFAULT NULL, distrito_id INT DEFAULT NULL, centro_poblado_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE instituciones');
        $this->addSql('ALTER TABLE institucion DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE persona CHANGE caso_desaparecido_total caso_desaparecido_total SMALLINT DEFAULT 0, CHANGE caso_desproteccion_total caso_desproteccion_total SMALLINT DEFAULT 0, CHANGE caso_trata_total caso_trata_total SMALLINT DEFAULT 0, CHANGE caso_violencia_total caso_violencia_total SMALLINT DEFAULT 0');
    }
}
