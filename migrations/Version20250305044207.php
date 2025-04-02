<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305044207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accion (id INT AUTO_INCREMENT NOT NULL, codigo VARCHAR(20) NOT NULL, fecha DATETIME NOT NULL, estado_id INT NOT NULL, institucion_id INT DEFAULT NULL, descripcion VARCHAR(1000) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE institucion ADD CONSTRAINT FK_F751F7C398260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE institucion ADD CONSTRAINT FK_F751F7C34E7121AF FOREIGN KEY (provincia_id) REFERENCES provincia (id)');
        $this->addSql('ALTER TABLE institucion ADD CONSTRAINT FK_F751F7C3E557397E FOREIGN KEY (distrito_id) REFERENCES distrito (id)');
        $this->addSql('ALTER TABLE institucion ADD CONSTRAINT FK_F751F7C3633A6385 FOREIGN KEY (centro_poblado_id) REFERENCES centro_poblado (id)');
        $this->addSql('ALTER TABLE institucion ADD CONSTRAINT FK_F751F7C37E3C61F9 FOREIGN KEY (owner_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_F751F7C398260155 ON institucion (region_id)');
        $this->addSql('CREATE INDEX IDX_F751F7C34E7121AF ON institucion (provincia_id)');
        $this->addSql('CREATE INDEX IDX_F751F7C3E557397E ON institucion (distrito_id)');
        $this->addSql('CREATE INDEX IDX_F751F7C3633A6385 ON institucion (centro_poblado_id)');
        $this->addSql('CREATE INDEX IDX_F751F7C37E3C61F9 ON institucion (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE accion');
        $this->addSql('ALTER TABLE institucion DROP FOREIGN KEY FK_F751F7C398260155');
        $this->addSql('ALTER TABLE institucion DROP FOREIGN KEY FK_F751F7C34E7121AF');
        $this->addSql('ALTER TABLE institucion DROP FOREIGN KEY FK_F751F7C3E557397E');
        $this->addSql('ALTER TABLE institucion DROP FOREIGN KEY FK_F751F7C3633A6385');
        $this->addSql('ALTER TABLE institucion DROP FOREIGN KEY FK_F751F7C37E3C61F9');
        $this->addSql('DROP INDEX IDX_F751F7C398260155 ON institucion');
        $this->addSql('DROP INDEX IDX_F751F7C34E7121AF ON institucion');
        $this->addSql('DROP INDEX IDX_F751F7C3E557397E ON institucion');
        $this->addSql('DROP INDEX IDX_F751F7C3633A6385 ON institucion');
        $this->addSql('DROP INDEX IDX_F751F7C37E3C61F9 ON institucion');
        $this->addSql('ALTER TABLE institucion CHANGE region_id region_id INT DEFAULT NULL');
    }
}
